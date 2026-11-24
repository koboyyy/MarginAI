<?php

use App\Enums\LaborRateType;
use App\Enums\OverheadCostType;
use App\Jobs\AnalyzePriceSimulation;
use App\Models\Business;
use App\Models\LaborCost;
use App\Models\OverheadCost;
use App\Models\Product;
use App\Models\RawMaterial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

test('price simulations save revenue profit and break even with direct labor as variable cost', function () {
    $owner = User::factory()->create();
    $business = Business::factory()->for($owner, 'owner')->create();
    $material = RawMaterial::factory()->for($business)->create([
        'unit_price' => '1000.0000',
        'wastage_percent' => '10.00',
    ]);
    $laborCost = LaborCost::factory()->for($business)->create([
        'rate_type' => LaborRateType::PerHour,
        'rate' => '50000.0000',
    ]);
    $fixedOverhead = OverheadCost::factory()->for($business)->create([
        'type' => OverheadCostType::Fixed,
        'amount' => '100000.0000',
    ]);
    $variableOverhead = OverheadCost::factory()->for($business)->create([
        'type' => OverheadCostType::Variable,
        'amount' => '5000.0000',
    ]);

    $product = $this->actingAs($owner)->postJson('/api/products', [
        'name' => 'Roti Cokelat',
        'batch_yield' => '10.00',
        'batches_per_month' => '10.00',
        'materials' => [
            ['raw_material_id' => $material->id, 'quantity_used' => '100.0000'],
        ],
        'labor_costs' => [
            ['labor_cost_id' => $laborCost->id, 'quantity_used' => '0.5000'],
        ],
        'overhead_costs' => [
            ['overhead_cost_id' => $fixedOverhead->id],
            ['overhead_cost_id' => $variableOverhead->id],
        ],
    ])->assertCreated()->json();

    Queue::fake([AnalyzePriceSimulation::class]);

    $this->postJson("/api/products/{$product['id']}/price-simulations", [
        'simulated_price' => '20000.00',
        'estimated_units_sold' => 100,
        'material_price_changes' => [
            ['raw_material_id' => $material->id, 'change_percent' => '10.00'],
        ],
    ])->assertCreated()
        ->assertJsonPath('simulated_price', '20000.00')
        ->assertJsonPath('projected_revenue', '2000000.00')
        ->assertJsonPath('projected_profit', '390000.00')
        ->assertJsonPath('variable_material_cost_per_unit', '12100.0000')
        ->assertJsonPath('variable_labor_cost_per_unit', '2500.0000')
        ->assertJsonPath('variable_overhead_cost_per_unit', '500.0000')
        ->assertJsonPath('variable_cost_per_unit', '15100.0000')
        ->assertJsonPath('fixed_cost_per_period', '100000.00')
        ->assertJsonPath('break_even_units', 21)
        ->assertJsonPath('material_price_changes.0.simulated_unit_price', '1100.0000');

    $this->assertDatabaseHas('price_simulations', [
        'product_id' => $product['id'],
        'projected_revenue' => '2000000.00',
        'projected_profit' => '390000.00',
        'break_even_units' => 21,
    ]);

    $this->getJson("/api/products/{$product['id']}/price-simulations")
        ->assertOk()
        ->assertJsonCount(1)
        ->assertJsonPath('0.projected_profit', '390000.00');
});

test('price simulations keep the formula price as the base and queue recommendation analysis', function () {
    $owner = User::factory()->create();
    $business = Business::factory()->for($owner, 'owner')->create([
        'default_margin_percent' => '30.00',
    ]);
    $material = RawMaterial::factory()->for($business)->create([
        'unit_price' => '1000.0000',
        'wastage_percent' => '0.00',
    ]);

    $product = $this->actingAs($owner)->postJson('/api/products', [
        'name' => 'Kue Pisang',
        'batch_yield' => '1.00',
        'batches_per_month' => '1.00',
        'materials' => [
            ['raw_material_id' => $material->id, 'quantity_used' => '1.0000'],
        ],
    ])->assertCreated()->json();

    Queue::fake([AnalyzePriceSimulation::class]);

    $simulation = $this->postJson("/api/products/{$product['id']}/price-simulations", [
        'simulated_price' => '1500.00',
        'estimated_units_sold' => 10,
        'competitor_prices' => ['1400.00', '1600.00'],
    ])->assertCreated()
        ->assertJsonPath('base_recommended_price', '1428.57')
        ->assertJsonPath('recommendation_status', 'pending')
        ->assertJsonPath('competitor_prices', ['1400.00', '1600.00'])
        ->json();

    Queue::assertPushed(AnalyzePriceSimulation::class, fn (AnalyzePriceSimulation $job): bool => $job->priceSimulationId === $simulation['id']);
    $this->assertDatabaseHas('price_simulations', [
        'id' => $simulation['id'],
        'base_recommended_price' => '1428.57',
        'recommendation_status' => 'pending',
    ]);
});

test('price simulations convert a markup target to its equivalent margin', function () {
    $owner = User::factory()->create();
    $business = Business::factory()->for($owner, 'owner')->create([
        'pricing_method' => 'markup',
        'default_margin_percent' => '50.00',
    ]);
    $product = Product::factory()->for($business)->create([
        'calculated_hpp' => '1000.00',
        'recommended_price' => '1500.00',
    ]);

    Queue::fake([AnalyzePriceSimulation::class]);

    $this->actingAs($owner)->postJson("/api/products/{$product->id}/price-simulations", [
        'simulated_price' => '1500.00',
        'estimated_units_sold' => 1,
    ])->assertCreated()
        ->assertJsonPath('base_recommended_price', '1500.00')
        ->assertJsonPath('target_margin_percent', '33.33');
});

test('price simulations reject invalid competitor prices without creating a simulation', function () {
    $owner = User::factory()->create();
    $business = Business::factory()->for($owner, 'owner')->create();
    $product = Product::factory()->for($business)->create();

    $this->actingAs($owner)->postJson("/api/products/{$product->id}/price-simulations", [
        'simulated_price' => '1000.00',
        'estimated_units_sold' => 10,
        'competitor_prices' => ['0.00'],
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['competitor_prices.0']);

    $this->assertDatabaseCount('price_simulations', 0);
});

test('price simulations reject material price changes for materials absent from the product', function () {
    $owner = User::factory()->create();
    $business = Business::factory()->for($owner, 'owner')->create();
    $productMaterial = RawMaterial::factory()->for($business)->create();
    $unrelatedMaterial = RawMaterial::factory()->for($business)->create();

    $product = $this->actingAs($owner)->postJson('/api/products', [
        'name' => 'Kue',
        'batch_yield' => '1.00',
        'batches_per_month' => '1.00',
        'materials' => [
            ['raw_material_id' => $productMaterial->id, 'quantity_used' => '1.0000'],
        ],
    ])->assertCreated()->json();

    $this->postJson("/api/products/{$product['id']}/price-simulations", [
        'simulated_price' => '2000.00',
        'estimated_units_sold' => 1,
        'material_price_changes' => [
            ['raw_material_id' => $unrelatedMaterial->id, 'change_percent' => '10.00'],
        ],
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['material_price_changes.0.raw_material_id']);

    $this->assertDatabaseCount('price_simulations', 0);
});

test('price simulations omit break even when price does not cover variable cost', function () {
    $owner = User::factory()->create();
    $business = Business::factory()->for($owner, 'owner')->create();
    $material = RawMaterial::factory()->for($business)->create([
        'unit_price' => '100.0000',
    ]);
    $fixedOverhead = OverheadCost::factory()->for($business)->create([
        'type' => OverheadCostType::Fixed,
        'amount' => '100.0000',
    ]);

    $product = $this->actingAs($owner)->postJson('/api/products', [
        'name' => 'Produk Impas',
        'batch_yield' => '1.00',
        'batches_per_month' => '1.00',
        'materials' => [
            ['raw_material_id' => $material->id, 'quantity_used' => '1.0000'],
        ],
        'overhead_costs' => [
            ['overhead_cost_id' => $fixedOverhead->id],
        ],
    ])->assertCreated()->json();

    Queue::fake([AnalyzePriceSimulation::class]);

    $this->postJson("/api/products/{$product['id']}/price-simulations", [
        'simulated_price' => '100.00',
        'estimated_units_sold' => 5,
    ])->assertCreated()
        ->assertJsonPath('variable_cost_per_unit', '100.0000')
        ->assertJsonPath('fixed_cost_per_period', '100.00')
        ->assertJsonPath('projected_profit', '-100.00')
        ->assertJsonPath('break_even_units', null);
});

test('price simulations return 404 for another business product', function () {
    $owner = User::factory()->create();
    Business::factory()->for($owner, 'owner')->create();
    $product = Product::factory()->create();

    $this->actingAs($owner)
        ->postJson("/api/products/{$product->id}/price-simulations", [
            'simulated_price' => '100.00',
            'estimated_units_sold' => 1,
        ])
        ->assertNotFound();

    $this->assertDatabaseCount('price_simulations', 0);
});

test('price simulations return 401 without authentication', function () {
    $this->postJson('/api/products/1/price-simulations', [
        'simulated_price' => '100.00',
        'estimated_units_sold' => 1,
    ])->assertUnauthorized();
});
