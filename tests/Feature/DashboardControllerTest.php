<?php

use App\Models\Business;
use App\Models\LaborCost;
use App\Models\OverheadCost;
use App\Models\Product;
use App\Models\RawMaterial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

test('dashboard aggregates current product KPIs and monthly simulation trends for its business', function () {
    $this->travelTo('2026-11-23 12:00:00');

    $owner = User::factory()->create();
    $business = Business::factory()->for($owner, 'owner')->create();
    $firstProduct = Product::factory()->for($business)->create([
        'calculated_hpp' => '100.00',
        'recommended_price' => '200.00',
        'selling_price' => '150.00',
    ]);
    $secondProduct = Product::factory()->for($business)->create([
        'calculated_hpp' => '300.00',
        'recommended_price' => '400.00',
        'selling_price' => null,
    ]);
    $material = RawMaterial::factory()->for($business)->create();
    $laborCost = LaborCost::factory()->for($business)->create();
    $overheadCost = OverheadCost::factory()->for($business)->create();
    $firstProduct->materials()->attach($material->id, [
        'quantity_used' => '1.0000',
        'subtotal_cost' => '100.0000',
    ]);
    $secondProduct->materials()->attach($material->id, [
        'quantity_used' => '1.0000',
        'subtotal_cost' => '300.0000',
    ]);
    $firstProduct->laborCosts()->attach($laborCost->id, [
        'quantity_used' => '1.0000',
        'subtotal_cost' => '10.0000',
    ]);
    $secondProduct->laborCosts()->attach($laborCost->id, [
        'quantity_used' => '1.0000',
        'subtotal_cost' => '30.0000',
    ]);
    $firstProduct->overheadCosts()->attach($overheadCost->id, [
        'subtotal_cost' => '20.0000',
    ]);
    $secondProduct->overheadCosts()->attach($overheadCost->id, [
        'subtotal_cost' => '40.0000',
    ]);
    $otherBusinessProduct = Product::factory()->create([
        'calculated_hpp' => '900.00',
        'recommended_price' => '1000.00',
        'selling_price' => '950.00',
    ]);
    $otherBusiness = $otherBusinessProduct->business;
    $otherBusinessMaterial = RawMaterial::factory()->for($otherBusiness)->create();
    $otherBusinessLaborCost = LaborCost::factory()->for($otherBusiness)->create();
    $otherBusinessOverheadCost = OverheadCost::factory()->for($otherBusiness)->create();
    $otherBusinessProduct->materials()->attach($otherBusinessMaterial->id, [
        'quantity_used' => '1.0000',
        'subtotal_cost' => '900.0000',
    ]);
    $otherBusinessProduct->laborCosts()->attach($otherBusinessLaborCost->id, [
        'quantity_used' => '1.0000',
        'subtotal_cost' => '900.0000',
    ]);
    $otherBusinessProduct->overheadCosts()->attach($otherBusinessOverheadCost->id, [
        'subtotal_cost' => '900.0000',
    ]);

    $createSimulation = function (
        Product $product,
        string $hpp,
        string $price,
        string $profit,
        string $createdAt,
    ): void {
        $simulation = $product->priceSimulations()->create([
            'simulated_price' => $price,
            'estimated_units_sold' => 1,
            'projected_revenue' => $price,
            'projected_profit' => $profit,
            'hpp_snapshot' => $hpp,
        ]);
        $simulation->created_at = Carbon::parse($createdAt);
        $simulation->save();
    };

    $createSimulation($firstProduct, '100.00', '200.00', '100.00', '2026-03-10 10:00:00');
    $createSimulation($secondProduct, '200.00', '300.00', '300.00', '2026-03-20 10:00:00');
    $createSimulation($firstProduct, '50.00', '100.00', '-25.00', '2026-04-15 10:00:00');
    $createSimulation($firstProduct, '10.00', '20.00', '10.00', '2025-11-30 10:00:00');
    $createSimulation($otherBusinessProduct, '900.00', '1000.00', '100.00', '2026-03-15 10:00:00');

    $this->actingAs($owner)->getJson('/api/dashboard')
        ->assertOk()
        ->assertJsonPath('period.from', '2025-12-01')
        ->assertJsonPath('period.to', '2026-11-23')
        ->assertJsonPath('kpis.product_count', 2)
        ->assertJsonPath('kpis.average_hpp', '200.00')
        ->assertJsonPath('kpis.average_recommended_price', '300.00')
        ->assertJsonPath('kpis.average_selling_price', '150.00')
        ->assertJsonPath('kpis.average_margin_percent', '33.33')
        ->assertJsonPath('kpis.average_material_cost_per_batch', '200.00')
        ->assertJsonPath('kpis.average_labor_cost_per_batch', '20.00')
        ->assertJsonPath('kpis.average_overhead_cost_per_batch', '30.00')
        ->assertJsonCount(12, 'trends')
        ->assertJsonPath('trends.3.month', '2026-03')
        ->assertJsonPath('trends.3.average_hpp', '150.00')
        ->assertJsonPath('trends.3.average_simulated_price', '250.00')
        ->assertJsonPath('trends.3.average_projected_profit', '200.00')
        ->assertJsonPath('trends.3.average_margin_percent', '41.67')
        ->assertJsonPath('trends.3.simulation_count', 2)
        ->assertJsonPath('trends.4.average_hpp', '50.00')
        ->assertJsonPath('trends.4.average_simulated_price', '100.00')
        ->assertJsonPath('trends.4.average_projected_profit', '-25.00')
        ->assertJsonPath('trends.4.average_margin_percent', '50.00')
        ->assertJsonPath('trends.4.simulation_count', 1)
        ->assertJsonPath('trends.0.simulation_count', 0)
        ->assertJsonPath('trends.0.average_hpp', null)
        ->assertJsonPath('trends.0.average_projected_profit', null);
});

test('dashboard returns 404 when the authenticated owner has no business', function () {
    $owner = User::factory()->create();

    $this->actingAs($owner)->getJson('/api/dashboard')->assertNotFound();
});

test('dashboard returns empty metrics when the business has no products', function () {
    $owner = User::factory()->create();
    Business::factory()->for($owner, 'owner')->create();

    $this->actingAs($owner)->getJson('/api/dashboard')
        ->assertOk()
        ->assertJsonPath('kpis.product_count', 0)
        ->assertJsonPath('kpis.average_hpp', null)
        ->assertJsonPath('kpis.average_margin_percent', null)
        ->assertJsonPath('kpis.average_material_cost_per_batch', null)
        ->assertJsonPath('kpis.average_labor_cost_per_batch', null)
        ->assertJsonPath('kpis.average_overhead_cost_per_batch', null)
        ->assertJsonCount(12, 'trends')
        ->assertJsonPath('trends.0.simulation_count', 0)
        ->assertJsonPath('trends.0.average_simulated_price', null)
        ->assertJsonPath('trends.0.average_projected_profit', null);
});

test('dashboard returns empty trends when the business has products but no simulations', function () {
    $owner = User::factory()->create();
    $business = Business::factory()->for($owner, 'owner')->create();
    Product::factory()->for($business)->create();

    $this->actingAs($owner)->getJson('/api/dashboard')
        ->assertOk()
        ->assertJsonPath('kpis.product_count', 1)
        ->assertJsonCount(12, 'trends')
        ->assertJsonPath('trends.11.simulation_count', 0)
        ->assertJsonPath('trends.11.average_hpp', null)
        ->assertJsonPath('trends.11.average_simulated_price', null)
        ->assertJsonPath('trends.11.average_projected_profit', null);
});

test('dashboard returns 401 without authentication', function () {
    $this->getJson('/api/dashboard')->assertUnauthorized();
});
