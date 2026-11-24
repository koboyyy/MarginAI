<?php

namespace App\Actions;

use App\Enums\OverheadCostType;
use App\Enums\PricingMethod;
use App\Models\PriceSimulation;
use App\Models\Product;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Illuminate\Validation\ValidationException;
use LogicException;

class SimulateProductPrice
{
    /**
     * @param  array{
     *     simulated_price: numeric-string,
     *     estimated_units_sold: int|string,
     *     material_price_changes?: array<int, array{
     *         raw_material_id: int|string,
     *         change_percent: numeric-string
     *     }>
     *     competitor_prices?: array<int, numeric-string>
     * }  $data
     */
    public function handle(Product $product, array $data): PriceSimulation
    {
        $product->load(['materials', 'laborCosts', 'overheadCosts']);
        $business = $product->business()->firstOrFail();
        $baseRecommendedPrice = $product->recommended_price
            ?? throw new LogicException('A price simulation requires a calculated recommended price.');
        $targetPercent = BigDecimal::of((string) $business->default_margin_percent);
        $targetMarginPercent = $business->pricing_method === PricingMethod::Markup
            ? $targetPercent
                ->multipliedBy('100')
                ->dividedBy($targetPercent->plus('100'), 2, RoundingMode::HalfUp)
            : $targetPercent;
        $changesByMaterialId = [];
        $materialPriceChanges = [];
        $competitorPrices = array_map(
            static fn (string|int $price): string => BigDecimal::of((string) $price)
                ->toScale(2, RoundingMode::HalfUp)
                ->toString(),
            $data['competitor_prices'] ?? [],
        );
        $materialsById = $product->materials->keyBy('id');

        foreach ($data['material_price_changes'] ?? [] as $index => $change) {
            $material = $materialsById->get((int) $change['raw_material_id']);

            if ($material === null) {
                throw ValidationException::withMessages([
                    "material_price_changes.{$index}.raw_material_id" => [
                        'Bahan baku yang diubah harus digunakan oleh produk ini.',
                    ],
                ]);
            }

            $originalUnitPrice = BigDecimal::of((string) $material->unit_price);
            $changePercent = BigDecimal::of((string) $change['change_percent']);
            $priceMultiplier = BigDecimal::one()->plus(
                $changePercent->dividedBy('100', 8, RoundingMode::HalfUp),
            );
            $simulatedUnitPrice = $originalUnitPrice
                ->multipliedBy($priceMultiplier)
                ->toScale(4, RoundingMode::HalfUp);
            $changesByMaterialId[$material->id] = $simulatedUnitPrice;
            $materialPriceChanges[] = [
                'raw_material_id' => $material->id,
                'original_unit_price' => $originalUnitPrice->toScale(4, RoundingMode::HalfUp)->toString(),
                'change_percent' => $changePercent->toScale(2, RoundingMode::HalfUp)->toString(),
                'simulated_unit_price' => $simulatedUnitPrice->toString(),
            ];
        }

        $materialCostPerBatch = BigDecimal::zero();

        foreach ($product->materials as $material) {
            $unitPrice = $changesByMaterialId[$material->id] ?? BigDecimal::of((string) $material->unit_price);
            $wastageMultiplier = BigDecimal::one()->plus(
                BigDecimal::of((string) $material->wastage_percent)
                    ->dividedBy('100', 8, RoundingMode::HalfUp),
            );
            $materialCostPerBatch = $materialCostPerBatch->plus(
                BigDecimal::of((string) $material->pivot->quantity_used)
                    ->multipliedBy($unitPrice)
                    ->multipliedBy($wastageMultiplier)
                    ->toScale(4, RoundingMode::HalfUp),
            );
        }

        $laborCostPerBatch = BigDecimal::zero();

        foreach ($product->laborCosts as $laborCost) {
            $laborCostPerBatch = $laborCostPerBatch->plus(
                BigDecimal::of((string) $laborCost->rate)
                    ->multipliedBy((string) $laborCost->pivot->quantity_used)
                    ->toScale(4, RoundingMode::HalfUp),
            );
        }

        $variableOverheadPerBatch = BigDecimal::zero();
        $fixedOverheadPerBatch = BigDecimal::zero();

        foreach ($product->overheadCosts as $overheadCost) {
            $subtotal = BigDecimal::of((string) $overheadCost->pivot->subtotal_cost);

            if ($overheadCost->type === OverheadCostType::Fixed) {
                $fixedOverheadPerBatch = $fixedOverheadPerBatch->plus($subtotal);
            } else {
                $variableOverheadPerBatch = $variableOverheadPerBatch->plus($subtotal);
            }
        }

        $batchYield = BigDecimal::of((string) $product->batch_yield);
        $variableMaterialCostPerUnit = $materialCostPerBatch
            ->dividedBy($batchYield, 4, RoundingMode::HalfUp);
        $variableLaborCostPerUnit = $laborCostPerBatch
            ->dividedBy($batchYield, 4, RoundingMode::HalfUp);
        $variableOverheadCostPerUnit = $variableOverheadPerBatch
            ->dividedBy($batchYield, 4, RoundingMode::HalfUp);
        $variableCostPerUnit = $variableMaterialCostPerUnit
            ->plus($variableLaborCostPerUnit)
            ->plus($variableOverheadCostPerUnit)
            ->toScale(4, RoundingMode::HalfUp);
        $fixedCostPerPeriod = $fixedOverheadPerBatch
            ->multipliedBy((string) $product->batches_per_month)
            ->toScale(2, RoundingMode::HalfUp);

        $simulatedPrice = BigDecimal::of($data['simulated_price']);
        $estimatedUnitsSold = (int) $data['estimated_units_sold'];
        $projectedRevenue = $simulatedPrice
            ->multipliedBy((string) $estimatedUnitsSold)
            ->toScale(2, RoundingMode::HalfUp);
        $projectedProfit = $projectedRevenue
            ->minus($variableCostPerUnit->multipliedBy((string) $estimatedUnitsSold))
            ->minus($fixedCostPerPeriod)
            ->toScale(2, RoundingMode::HalfUp);
        $contributionPerUnit = $simulatedPrice->minus($variableCostPerUnit);
        $breakEvenUnits = $contributionPerUnit->isGreaterThan('0')
            ? $fixedCostPerPeriod
                ->dividedBy($contributionPerUnit, 0, RoundingMode::Ceiling)
                ->toBigInteger()
                ->toString()
            : null;

        return $product->priceSimulations()->create([
            'simulated_price' => $simulatedPrice->toScale(2, RoundingMode::HalfUp)->toString(),
            'estimated_units_sold' => $estimatedUnitsSold,
            'projected_revenue' => $projectedRevenue->toString(),
            'projected_profit' => $projectedProfit->toString(),
            'base_recommended_price' => $baseRecommendedPrice,
            'hpp_snapshot' => $product->calculated_hpp,
            'target_margin_percent' => $targetMarginPercent->toScale(2, RoundingMode::HalfUp)->toString(),
            'competitor_prices' => $competitorPrices,
            'recommendation_status' => 'pending',
            'material_price_changes' => $materialPriceChanges,
            'variable_material_cost_per_unit' => $variableMaterialCostPerUnit->toScale(4, RoundingMode::HalfUp)->toString(),
            'variable_labor_cost_per_unit' => $variableLaborCostPerUnit->toScale(4, RoundingMode::HalfUp)->toString(),
            'variable_overhead_cost_per_unit' => $variableOverheadCostPerUnit->toScale(4, RoundingMode::HalfUp)->toString(),
            'variable_cost_per_unit' => $variableCostPerUnit->toString(),
            'fixed_cost_per_period' => $fixedCostPerPeriod->toString(),
            'break_even_units' => $breakEvenUnits,
        ]);
    }
}
