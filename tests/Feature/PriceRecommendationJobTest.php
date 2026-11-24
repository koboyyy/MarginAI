<?php

use App\Ai\Agents\PriceRecommendation;
use App\Jobs\AnalyzePriceSimulation;
use App\Models\Business;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Ai\Exceptions\InsufficientCreditsException;
use Laravel\Ai\Exceptions\ProviderConnectionException;
use Laravel\Ai\Exceptions\ProviderOverloadedException;
use Laravel\Ai\Exceptions\RateLimitedException;
use Laravel\Ai\Prompts\AgentPrompt;

uses(RefreshDatabase::class);

test('job recommends a psychological price above the formula base and compares manual competitor prices', function () {
    $product = Product::factory()->for(Business::factory()->create())->create();
    $simulation = $product->priceSimulations()->create([
        'simulated_price' => '21000.00',
        'estimated_units_sold' => 10,
        'projected_revenue' => '210000.00',
        'projected_profit' => '50000.00',
        'base_recommended_price' => '21428.57',
        'hpp_snapshot' => '15000.00',
        'target_margin_percent' => '30.00',
        'competitor_prices' => ['20000.00', '22000.00'],
        'recommendation_status' => 'pending',
    ]);

    PriceRecommendation::fake([[
        'recommended_price' => '21900.00',
        'pricing_explanation' => 'Harga mudah diingat dan tetap memenuhi margin.',
        'risk_explanation' => 'Perhatikan perubahan biaya bahan dan posisi harga kompetitor.',
    ]])->preventStrayPrompts();

    (new AnalyzePriceSimulation($simulation->id))->handle();

    $simulation->refresh();
    $this->assertModelExists($simulation);
    expect($simulation->recommendation_status)->toBe('completed');
    expect($simulation->psychological_price)->toBe('21900.00');
    expect($simulation->ai_notes)->toBe('Alasan harga: Harga mudah diingat dan tetap memenuhi margin. Risiko margin: Perhatikan perubahan biaya bahan dan posisi harga kompetitor.');
    expect($simulation->margin_risk)->toBe('medium');
    expect($simulation->competition_analysis)->toMatchArray([
        'status' => 'analyzed',
        'position' => 'within_competitor_range',
        'competitor_count' => 2,
        'lowest_price' => '20000.00',
        'highest_price' => '22000.00',
        'average_price' => '21000.00',
    ]);
    PriceRecommendation::assertPrompted(function (AgentPrompt $prompt): bool {
        return $prompt->contains('"business_category": "kuliner"')
            && $prompt->contains('"hpp_per_unit": "15000.00"')
            && $prompt->contains('"target_margin_percent": "30.00"')
            && $prompt->contains('"minimum_formula_price": "21428.57"')
            && $prompt->contains('"simulated_price": "21000.00"')
            && $prompt->contains('"competitor_prices": [')
            && $prompt->contains('"20000.00"')
            && $prompt->contains('"22000.00"')
            && $prompt->timeout === (int) config('ai.providers.gemini.timeout', 120);
    });
});

test('job classifies margin and competition analysis for loss, profit, and missing competitor data', function (
    string $simulatedPrice,
    ?array $competitorPrices,
    string $expectedRisk,
    string $expectedStatus,
    ?string $expectedPosition,
) {
    $product = Product::factory()->create();
    $simulation = $product->priceSimulations()->create([
        'simulated_price' => $simulatedPrice,
        'estimated_units_sold' => 10,
        'projected_revenue' => '0.00',
        'projected_profit' => '0.00',
        'base_recommended_price' => '21428.57',
        'hpp_snapshot' => '15000.00',
        'target_margin_percent' => '30.00',
        'competitor_prices' => $competitorPrices,
        'recommendation_status' => 'pending',
    ]);

    PriceRecommendation::fake([[
        'recommended_price' => '21900.00',
        'pricing_explanation' => 'Harga mempertahankan margin.',
        'risk_explanation' => 'Pantau perubahan biaya.',
    ]])->preventStrayPrompts();

    (new AnalyzePriceSimulation($simulation->id))->handle();

    $simulation->refresh();
    expect($simulation->margin_risk)->toBe($expectedRisk);
    expect($simulation->competition_analysis['status'])->toBe($expectedStatus);
    expect($simulation->competition_analysis['position'])->toBe($expectedPosition);
})->with([
    'loss below competitors' => ['15000.00', ['18000.00', '20000.00'], 'high', 'analyzed', 'below_competitors'],
    'target margin reached above competitors' => ['22000.00', ['18000.00', '20000.00'], 'low', 'analyzed', 'above_competitors'],
    'no competitor prices supplied' => ['22000.00', null, 'low', 'unavailable', null],
]);

test('job does not mark a recommendation complete when the AI provider fails', function () {
    $simulation = Product::factory()->create()->priceSimulations()->create([
        'simulated_price' => '21000.00',
        'estimated_units_sold' => 10,
        'projected_revenue' => '210000.00',
        'projected_profit' => '50000.00',
        'base_recommended_price' => '21428.57',
        'hpp_snapshot' => '15000.00',
        'target_margin_percent' => '30.00',
        'recommendation_status' => 'pending',
    ]);
    $job = new AnalyzePriceSimulation($simulation->id);

    PriceRecommendation::fake(fn (): never => throw new RuntimeException('Provider timeout.'))
        ->preventStrayPrompts();

    expect(fn () => $job->handle())->toThrow(RuntimeException::class, 'Provider timeout.');

    $simulation->refresh();
    expect($simulation->recommendation_status)->toBe('processing');
    expect($simulation->psychological_price)->toBeNull();
    expect($simulation->ai_notes)->toBeNull();

    $job->failed(new RuntimeException('Provider timeout.'));

    $simulation->refresh();
    expect($simulation->recommendation_status)->toBe('failed');
    expect($simulation->recommendation_error)->toContain('timeout');
});

test('job rejects an AI price below the target margin formula', function () {
    $simulation = Product::factory()->create()->priceSimulations()->create([
        'simulated_price' => '21000.00',
        'estimated_units_sold' => 10,
        'projected_revenue' => '210000.00',
        'projected_profit' => '50000.00',
        'base_recommended_price' => '21428.57',
        'hpp_snapshot' => '15000.00',
        'target_margin_percent' => '30.00',
        'recommendation_status' => 'pending',
    ]);

    PriceRecommendation::fake([[
        'recommended_price' => '20000.00',
        'pricing_explanation' => 'Harga di bawah target.',
        'risk_explanation' => 'Margin tidak tercapai.',
    ]])->preventStrayPrompts();

    expect(fn () => (new AnalyzePriceSimulation($simulation->id))->handle())
        ->toThrow(UnexpectedValueException::class, 'outside the acceptable price range');

    $simulation->refresh();
    expect($simulation->recommendation_status)->toBe('processing');
    expect($simulation->psychological_price)->toBeNull();
});

test('job records correct and descriptive error messages for different failure types', function (
    Throwable $exception,
    string $expectedKeyword,
) {
    $simulation = Product::factory()->create()->priceSimulations()->create([
        'simulated_price' => '21000.00',
        'estimated_units_sold' => 10,
        'projected_revenue' => '210000.00',
        'projected_profit' => '50000.00',
        'recommendation_status' => 'pending',
    ]);

    (new AnalyzePriceSimulation($simulation->id))->failed($exception);

    $simulation->refresh();
    expect($simulation->recommendation_status)->toBe('failed');
    expect($simulation->recommendation_error)->toContain($expectedKeyword);
})->with([
    'provider overloaded' => [
        fn () => ProviderOverloadedException::forProvider('gemini'),
        'overloaded',
    ],
    'connection timeout' => [
        fn () => ProviderConnectionException::forProvider('gemini'),
        'timeout',
    ],
    'rate limit exceeded' => [
        fn () => RateLimitedException::forProvider('gemini'),
        'rate limit',
    ],
    'insufficient credits' => [
        fn () => InsufficientCreditsException::forProvider('gemini'),
        'kuota',
    ],
    'price outside margin' => [
        fn () => new UnexpectedValueException('The AI recommendation is outside the acceptable price range.'),
        'luar batas margin',
    ],
    'invalid price format' => [
        fn () => new UnexpectedValueException('The AI recommendation returned an invalid price.'),
        'tidak valid',
    ],
    'missing explanation' => [
        fn () => new UnexpectedValueException('The AI recommendation is missing its explanations.'),
        'tidak lengkap',
    ],
    'missing snapshots' => [
        fn () => new LogicException('The price simulation is missing recommendation snapshots.'),
        'tidak lengkap',
    ],
]);
