<?php

use App\Enums\LaborRateType;
use App\Enums\OverheadCostType;
use App\Models\Business;
use App\Models\LaborCost;
use App\Models\OverheadCost;
use App\Models\Product;
use App\Models\RawMaterial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('product formulas calculate cost price and fixed overhead per batch', function () {
    $owner = User::factory()->create();
    $business = Business::factory()->for($owner, 'owner')->create([
        'default_margin_percent' => '30.00',
    ]);
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

    $this->actingAs($owner)->postJson('/api/products', [
        'name' => 'Roti Cokelat',
        'sku' => 'ROT-001',
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
    ])->assertCreated()
        ->assertJsonPath('calculated_hpp', '15000.00')
        ->assertJsonPath('recommended_price', '21428.57')
        ->assertJsonPath('labor_cost_per_batch', '25000.0000')
        ->assertJsonPath('overhead_cost_per_batch', '15000.0000');

    $this->assertDatabaseHas('product_materials', [
        'product_id' => Product::query()->value('id'),
        'raw_material_id' => $material->id,
        'subtotal_cost' => '110000.0000',
    ]);
    $this->assertDatabaseHas('product_overhead_costs', [
        'overhead_cost_id' => $fixedOverhead->id,
        'subtotal_cost' => '10000.0000',
    ]);
});

test('product formulas reject masters from another business', function () {
    $owner = User::factory()->create();
    Business::factory()->for($owner, 'owner')->create();
    $otherBusiness = Business::factory()->create();
    $otherMaterial = RawMaterial::factory()->for($otherBusiness)->create();

    $this->actingAs($owner)->postJson('/api/products', [
        'name' => 'Produk Asing',
        'batch_yield' => '1.00',
        'batches_per_month' => '1.00',
        'materials' => [
            ['raw_material_id' => $otherMaterial->id, 'quantity_used' => '1.0000'],
        ],
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['materials.0.raw_material_id']);

    $this->assertDatabaseMissing('products', ['name' => 'Produk Asing']);
});

test('product update keeps formula components omitted from a partial request', function () {
    $owner = User::factory()->create();
    $business = Business::factory()->for($owner, 'owner')->create();
    $material = RawMaterial::factory()->for($business)->create([
        'unit_price' => '500.0000',
    ]);
    $product = $this->actingAs($owner)->postJson('/api/products', [
        'name' => 'Kue',
        'batch_yield' => '5.00',
        'batches_per_month' => '2.00',
        'materials' => [
            ['raw_material_id' => $material->id, 'quantity_used' => '10.0000'],
        ],
    ])->assertCreated()->json();

    $this->patchJson("/api/products/{$product['id']}", [
        'name' => 'Kue Baru',
    ])->assertOk()
        ->assertJsonPath('name', 'Kue Baru')
        ->assertJsonPath('calculated_hpp', '1000.00');

    $this->assertDatabaseHas('product_materials', [
        'product_id' => $product['id'],
        'raw_material_id' => $material->id,
    ]);
});

test('owners cannot read another business product', function () {
    $owner = User::factory()->create();
    Business::factory()->for($owner, 'owner')->create();
    $product = Product::factory()->create();

    $this->actingAs($owner)->getJson("/api/products/{$product->id}")
        ->assertNotFound();
});

test('fixed overhead is allocated across the linked monthly batch volume', function () {
    $owner = User::factory()->create();
    $business = Business::factory()->for($owner, 'owner')->create([
        'default_margin_percent' => '0.00',
    ]);
    $material = RawMaterial::factory()->for($business)->create([
        'unit_price' => '1000.0000',
        'wastage_percent' => '0.00',
    ]);
    $overhead = OverheadCost::factory()->for($business)->create([
        'type' => OverheadCostType::Fixed,
        'amount' => '100000.0000',
    ]);

    $firstProduct = $this->actingAs($owner)->postJson('/api/products', [
        'name' => 'Produk A',
        'batch_yield' => '10.00',
        'batches_per_month' => '10.00',
        'materials' => [
            ['raw_material_id' => $material->id, 'quantity_used' => '1.0000'],
        ],
        'overhead_costs' => [
            ['overhead_cost_id' => $overhead->id],
        ],
    ])->assertCreated()->json();

    $secondProduct = $this->postJson('/api/products', [
        'name' => 'Produk B',
        'batch_yield' => '10.00',
        'batches_per_month' => '30.00',
        'materials' => [
            ['raw_material_id' => $material->id, 'quantity_used' => '1.0000'],
        ],
        'overhead_costs' => [
            ['overhead_cost_id' => $overhead->id],
        ],
    ])->assertCreated()->json();

    $this->assertDatabaseHas('product_overhead_costs', [
        'product_id' => $firstProduct['id'],
        'overhead_cost_id' => $overhead->id,
        'subtotal_cost' => '2500.0000',
    ]);
    $this->assertDatabaseHas('product_overhead_costs', [
        'product_id' => $secondProduct['id'],
        'overhead_cost_id' => $overhead->id,
        'subtotal_cost' => '2500.0000',
    ]);
    $this->getJson("/api/products/{$firstProduct['id']}")
        ->assertOk()
        ->assertJsonPath('calculated_hpp', '350.00');
});
