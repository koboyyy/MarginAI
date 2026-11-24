<?php

use App\Models\Business;
use App\Models\OverheadCost;
use App\Models\RawMaterial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('owners can create raw material labor and overhead masters', function () {
    $owner = User::factory()->create();
    Business::factory()->for($owner, 'owner')->create();

    $this->actingAs($owner)->postJson('/api/raw-materials', [
        'name' => 'Tepung',
        'unit' => 'kg',
        'unit_price' => '12000.2500',
        'wastage_percent' => '2.50',
    ])->assertCreated()
        ->assertJsonPath('unit_price', '12000.2500');

    $this->actingAs($owner)->postJson('/api/labor-costs', [
        'name' => 'Operator Produksi',
        'rate_type' => 'per_hour',
        'rate' => '25000.0000',
    ])->assertCreated()
        ->assertJsonPath('rate_type', 'per_hour');

    $this->actingAs($owner)->postJson('/api/overhead-costs', [
        'name' => 'Listrik',
        'type' => 'fixed',
        'amount' => '150000.0000',
    ])->assertCreated()
        ->assertJsonPath('amount', '150000.0000');

    $this->assertDatabaseHas('raw_materials', [
        'business_id' => $owner->business->id,
        'name' => 'Tepung',
    ]);
    $this->assertDatabaseHas('labor_costs', [
        'business_id' => $owner->business->id,
        'name' => 'Operator Produksi',
    ]);
    $this->assertDatabaseHas('overhead_costs', [
        'business_id' => $owner->business->id,
        'name' => 'Listrik',
    ]);
});

test('owners cannot read another business raw material', function () {
    $owner = User::factory()->create();
    Business::factory()->for($owner, 'owner')->create();
    $otherBusiness = Business::factory()->create();
    $material = RawMaterial::factory()->for($otherBusiness)->create();

    $this->actingAs($owner)->getJson("/api/raw-materials/{$material->id}")
        ->assertNotFound();
});

test('updating and deleting overhead recalculates products that use it', function () {
    $owner = User::factory()->create();
    $business = Business::factory()->for($owner, 'owner')->create();
    $overheadCost = OverheadCost::factory()->for($business)->create([
        'name' => 'Sewa dapur',
        'type' => 'fixed',
        'amount' => '1000.0000',
    ]);
    $material = RawMaterial::factory()->for($business)->create([
        'unit_price' => '0.0000',
    ]);
    $product = $this->actingAs($owner)->postJson('/api/products', [
        'name' => 'Roti',
        'batch_yield' => '2.00',
        'batches_per_month' => '1.00',
        'materials' => [
            ['raw_material_id' => $material->id, 'quantity_used' => '1.0000'],
        ],
        'overhead_costs' => [
            ['overhead_cost_id' => $overheadCost->id],
        ],
    ])->assertCreated()
        ->assertJsonPath('calculated_hpp', '500.00')
        ->json();

    $this->putJson("/api/overhead-costs/{$overheadCost->id}", [
        'name' => 'Gas produksi',
        'type' => 'variable',
        'amount' => '2000.0000',
    ])->assertOk()
        ->assertJsonPath('name', 'Gas produksi')
        ->assertJsonPath('amount', '2000.0000');

    $this->assertDatabaseHas('overhead_costs', [
        'id' => $overheadCost->id,
        'type' => 'variable',
        'amount' => '2000.0000',
    ]);
    $this->getJson("/api/products/{$product['id']}")
        ->assertOk()
        ->assertJsonPath('calculated_hpp', '1000.00');

    $this->deleteJson("/api/overhead-costs/{$overheadCost->id}")
        ->assertNoContent();

    $this->assertDatabaseMissing('overhead_costs', ['id' => $overheadCost->id]);
    $this->getJson("/api/products/{$product['id']}")
        ->assertOk()
        ->assertJsonPath('calculated_hpp', '0.00');
});

test('owners cannot update another business overhead', function () {
    $owner = User::factory()->create();
    Business::factory()->for($owner, 'owner')->create();
    $otherBusiness = Business::factory()->create();
    $overheadCost = OverheadCost::factory()->for($otherBusiness)->create([
        'amount' => '150000.0000',
    ]);

    $this->actingAs($owner)->putJson("/api/overhead-costs/{$overheadCost->id}", [
        'name' => 'Biaya milik usaha lain',
        'type' => 'fixed',
        'amount' => '1.0000',
    ])->assertNotFound();

    $this->assertDatabaseHas('overhead_costs', [
        'id' => $overheadCost->id,
        'amount' => '150000.0000',
    ]);
});

test('invalid material wastage is rejected without saving', function () {
    $owner = User::factory()->create();
    Business::factory()->for($owner, 'owner')->create();

    $this->actingAs($owner)->postJson('/api/raw-materials', [
        'name' => 'Minyak',
        'unit' => 'liter',
        'unit_price' => '18000.0000',
        'wastage_percent' => '100.01',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['wastage_percent']);

    $this->assertDatabaseMissing('raw_materials', ['name' => 'Minyak']);
});

test('raw material rejects units outside the SRS choices', function () {
    $owner = User::factory()->create();
    Business::factory()->for($owner, 'owner')->create();

    $this->actingAs($owner)->postJson('/api/raw-materials', [
        'name' => 'Gula',
        'unit' => 'karung',
        'unit_price' => '15000.0000',
        'wastage_percent' => '1.00',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['unit']);

    $this->assertDatabaseMissing('raw_materials', ['name' => 'Gula']);
});

test('updating a material recalculates products that use it', function () {
    $owner = User::factory()->create();
    $business = Business::factory()->for($owner, 'owner')->create();
    $material = RawMaterial::factory()->for($business)->create([
        'unit_price' => '500.0000',
        'wastage_percent' => '0.00',
    ]);
    $product = $this->actingAs($owner)->postJson('/api/products', [
        'name' => 'Kue Pisang',
        'batch_yield' => '5.00',
        'batches_per_month' => '2.00',
        'materials' => [
            ['raw_material_id' => $material->id, 'quantity_used' => '10.0000'],
        ],
    ])->assertCreated()->json();

    $this->putJson("/api/raw-materials/{$material->id}", [
        'name' => $material->name,
        'unit' => $material->unit,
        'unit_price' => '600.0000',
        'wastage_percent' => '0.00',
    ])->assertOk()
        ->assertJsonPath('unit_price', '600.0000');

    $this->getJson("/api/products/{$product['id']}")
        ->assertOk()
        ->assertJsonPath('calculated_hpp', '1200.00');
});

test('a material used in a product formula cannot be deleted', function () {
    $owner = User::factory()->create();
    $business = Business::factory()->for($owner, 'owner')->create();
    $material = RawMaterial::factory()->for($business)->create();
    $this->actingAs($owner)->postJson('/api/products', [
        'name' => 'Roti',
        'batch_yield' => '10.00',
        'batches_per_month' => '1.00',
        'materials' => [
            ['raw_material_id' => $material->id, 'quantity_used' => '1.0000'],
        ],
    ])->assertCreated();

    $this->deleteJson("/api/raw-materials/{$material->id}")
        ->assertConflict()
        ->assertJsonPath(
            'message',
            'Bahan baku yang digunakan dalam formula produk tidak dapat dihapus.',
        );

    $this->assertModelExists($material);
});
