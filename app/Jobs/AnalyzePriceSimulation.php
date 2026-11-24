<?php

namespace App\Jobs;

use App\Ai\Agents\PriceRecommendation;
use App\Models\PriceSimulation;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Laravel\Ai\Exceptions\InsufficientCreditsException;
use Laravel\Ai\Exceptions\InvalidApiKeyException;
use Laravel\Ai\Exceptions\ProviderConnectionException;
use Laravel\Ai\Exceptions\ProviderOverloadedException;
use Laravel\Ai\Exceptions\RateLimitedException;
use LogicException;
use Throwable;
use UnexpectedValueException;

class AnalyzePriceSimulation implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [10, 30];

    public int $timeout = 300;

    public function __construct(public int $priceSimulationId) {}

    public function handle(): void
    {
        $simulation = PriceSimulation::query()->findOrFail($this->priceSimulationId);

        if (! in_array($simulation->recommendation_status, ['pending', 'processing'], true)) {
            return;
        }

        if ($simulation->recommendation_status !== 'processing') {
            $simulation->forceFill([
                'recommendation_status' => 'processing',
                'recommendation_error' => null,
            ])->save();
        }

        if (
            $simulation->base_recommended_price === null
            || $simulation->hpp_snapshot === null
            || $simulation->target_margin_percent === null
        ) {
            throw new LogicException('The price simulation is missing recommendation snapshots.');
        }

        $simulation->loadMissing('product.business');
        $business = $simulation->product->business;
        $baseRecommendedPrice = BigDecimal::of($simulation->base_recommended_price);
        $simulatedPrice = BigDecimal::of($simulation->simulated_price);
        $hpp = BigDecimal::of($simulation->hpp_snapshot);
        $targetMarginPercent = BigDecimal::of($simulation->target_margin_percent);
        $promptData = [
            'business_category' => $business->category->value,
            'hpp_per_unit' => $hpp->toString(),
            'target_margin_percent' => $targetMarginPercent->toString(),
            'minimum_formula_price' => $baseRecommendedPrice->toString(),
            'simulated_price' => $simulatedPrice->toString(),
            'competitor_prices' => $simulation->competitor_prices ?? [],
        ];
        $recommendation = (new PriceRecommendation)->prompt(
            'Buat satu rekomendasi harga jual psikologis untuk data berikut. '
                .'Harga rekomendasi wajib minimal sebesar minimum_formula_price dan lebih besar dari HPP. '
                .'Jangan mengubah atau mengabaikan angka input. Jika data kompetitor kosong, nyatakan bahwa '
                ."perbandingan kompetitor tidak tersedia.\n\n"
                .json_encode($promptData, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR),
            timeout: (int) config('ai.providers.gemini.timeout', 120),
        );
        $recommendedPrice = $this->validatedRecommendedPrice(
            $recommendation['recommended_price'] ?? null,
            $baseRecommendedPrice,
            $hpp,
        );
        $pricingExplanation = $recommendation['pricing_explanation'] ?? null;
        $riskExplanation = $recommendation['risk_explanation'] ?? null;

        if (! is_string($pricingExplanation) || trim($pricingExplanation) === ''
            || ! is_string($riskExplanation) || trim($riskExplanation) === '') {
            throw new UnexpectedValueException('The AI recommendation is missing its explanations.');
        }

        $marginRisk = $this->marginRisk($simulatedPrice, $hpp, $targetMarginPercent);
        $competitionAnalysis = $this->competitionAnalysis(
            $simulation->competitor_prices,
            $simulatedPrice,
        );

        $simulation->forceFill([
            'psychological_price' => $recommendedPrice->toScale(2, RoundingMode::HalfUp)->toString(),
            'margin_risk' => $marginRisk,
            'competition_analysis' => $competitionAnalysis,
            'ai_notes' => 'Alasan harga: '.trim($pricingExplanation).' Risiko margin: '.trim($riskExplanation),
            'recommendation_status' => 'completed',
            'recommendation_error' => null,
        ])->save();
    }

    public function failed(?Throwable $exception): void
    {
        $errorMessage = $this->determineErrorMessage($exception);

        PriceSimulation::query()
            ->whereKey($this->priceSimulationId)
            ->whereIn('recommendation_status', ['pending', 'processing'])
            ->update([
                'recommendation_status' => 'failed',
                'recommendation_error' => $errorMessage,
            ]);
    }

    private function determineErrorMessage(?Throwable $exception): string
    {
        if ($exception === null) {
            return 'Terjadi kendala yang tidak diketahui saat memproses rekomendasi harga.';
        }

        $current = $exception;
        while ($current !== null) {
            if ($current instanceof ProviderOverloadedException) {
                return 'Server AI (Gemini) sedang mengalami lonjakan beban trafik (overloaded). Silakan coba beberapa saat lagi.';
            }

            if (
                $current instanceof ProviderConnectionException
                || $current instanceof ConnectionException
            ) {
                return 'Koneksi ke server AI terputus atau batas waktu habis (timeout). Silakan periksa jaringan dan coba lagi.';
            }

            if ($current instanceof RateLimitedException) {
                return 'Batas panggilan API AI (rate limit) telah tercapai. Harap tunggu sesaat sebelum mencoba lagi.';
            }

            if ($current instanceof InsufficientCreditsException) {
                return 'Kredit atau kuota API AI telah habis atau tidak mencukupi.';
            }

            if ($current instanceof InvalidApiKeyException) {
                return 'Kunci API (API Key) AI tidak valid atau belum dikonfigurasi.';
            }

            if ($current instanceof RequestException && $current->response !== null) {
                $status = $current->response->status();
                if ($status === 503) {
                    return 'Server AI (Gemini) sedang mengalami lonjakan beban trafik (overloaded). Silakan coba beberapa saat lagi.';
                }
                if ($status === 429) {
                    return 'Batas panggilan API AI (rate limit) telah tercapai. Harap tunggu sesaat sebelum mencoba lagi.';
                }
            }

            $current = $current->getPrevious();
        }

        if ($exception instanceof UnexpectedValueException) {
            $message = $exception->getMessage();
            if (str_contains($message, 'acceptable price range')) {
                return 'Rekomendasi harga dari AI berada di luar batas margin yang diizinkan.';
            }
            if (str_contains($message, 'invalid price')) {
                return 'Format harga rekomendasi yang diberikan oleh AI tidak valid.';
            }
            if (str_contains($message, 'missing its explanations')) {
                return 'Format penjelasan dari AI tidak lengkap.';
            }

            return 'Format respons AI tidak sesuai: '.$message;
        }

        if ($exception instanceof LogicException) {
            return 'Data simulasi harga tidak lengkap untuk dianalisis oleh AI.';
        }

        $msg = $exception->getMessage();
        $lower = strtolower($msg);
        if (str_contains($lower, 'overload') || str_contains($lower, 'high demand')) {
            return 'Server AI sedang mengalami lonjakan beban trafik (overloaded). Silakan coba beberapa saat lagi.';
        }
        if (str_contains($lower, 'timeout') || str_contains($lower, 'timed out')) {
            return 'Koneksi ke server AI mengalami batas waktu habis (timeout). Silakan coba beberapa saat lagi.';
        }

        return 'Gagal memproses rekomendasi AI: '.$msg;
    }

    private function validatedRecommendedPrice(
        mixed $value,
        BigDecimal $minimumFormulaPrice,
        BigDecimal $hpp,
    ): BigDecimal {
        if (! is_string($value) || preg_match('/^\d+(?:\.\d{1,2})?$/D', $value) !== 1) {
            throw new UnexpectedValueException('The AI recommendation returned an invalid price.');
        }

        $recommendedPrice = BigDecimal::of($value);

        if (
            $recommendedPrice->isLessThan($minimumFormulaPrice)
            || $recommendedPrice->isLessThanOrEqualTo($hpp)
            || $recommendedPrice->isGreaterThan('9999999999999.99')
        ) {
            throw new UnexpectedValueException('The AI recommendation is outside the acceptable price range.');
        }

        return $recommendedPrice;
    }

    private function marginRisk(
        BigDecimal $simulatedPrice,
        BigDecimal $hpp,
        BigDecimal $targetMarginPercent,
    ): string {
        if ($simulatedPrice->isLessThanOrEqualTo($hpp)) {
            return 'high';
        }

        $actualMarginPercent = $simulatedPrice
            ->minus($hpp)
            ->dividedBy($simulatedPrice, 8, RoundingMode::HalfUp)
            ->multipliedBy('100');

        return $actualMarginPercent->isLessThan($targetMarginPercent) ? 'medium' : 'low';
    }

    /**
     * @param  array<int, string>|null  $competitorPrices
     * @return array{
     *     status: string,
     *     position: string|null,
     *     competitor_count: int,
     *     lowest_price: string|null,
     *     highest_price: string|null,
     *     average_price: string|null
     * }
     */
    private function competitionAnalysis(?array $competitorPrices, BigDecimal $simulatedPrice): array
    {
        if ($competitorPrices === null || $competitorPrices === []) {
            return [
                'status' => 'unavailable',
                'position' => null,
                'competitor_count' => 0,
                'lowest_price' => null,
                'highest_price' => null,
                'average_price' => null,
            ];
        }

        $lowestPrice = null;
        $highestPrice = null;
        $totalPrice = BigDecimal::zero();

        foreach ($competitorPrices as $competitorPrice) {
            $price = BigDecimal::of($competitorPrice);
            $lowestPrice = $lowestPrice === null || $price->isLessThan($lowestPrice)
                ? $price
                : $lowestPrice;
            $highestPrice = $highestPrice === null || $price->isGreaterThan($highestPrice)
                ? $price
                : $highestPrice;
            $totalPrice = $totalPrice->plus($price);
        }

        $position = $simulatedPrice->isLessThan($lowestPrice)
            ? 'below_competitors'
            : ($simulatedPrice->isGreaterThan($highestPrice) ? 'above_competitors' : 'within_competitor_range');
        $averagePrice = $totalPrice->dividedBy(
            (string) count($competitorPrices),
            2,
            RoundingMode::HalfUp,
        );

        return [
            'status' => 'analyzed',
            'position' => $position,
            'competitor_count' => count($competitorPrices),
            'lowest_price' => $lowestPrice->toScale(2, RoundingMode::HalfUp)->toString(),
            'highest_price' => $highestPrice->toScale(2, RoundingMode::HalfUp)->toString(),
            'average_price' => $averagePrice->toString(),
        ];
    }
}
