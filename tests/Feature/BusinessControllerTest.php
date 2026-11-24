<?php

use App\Models\Business;
use App\Models\RawMaterial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated owners can create one business profile', function () {
    $owner = User::factory()->create();

    $this->actingAs($owner)->postJson('/api/business', [
        'name' => 'Dapur Nusantara',
        'category' => 'kuliner',
        'default_margin_percent' => '30.00',
    ])->assertCreated()
        ->assertJsonPath('name', 'Dapur Nusantara')
        ->assertJsonPath('default_margin_percent', '30.00');

    $this->actingAs($owner)->postJson('/api/business', [
        'name' => 'Bisnis Kedua',
        'category' => 'kuliner',
        'default_margin_percent' => '30.00',
    ])->assertConflict();

    $this->assertDatabaseCount('businesses', 1);
});

test('changing the default margin recalculates product recommendations', function () {
    $owner = User::factory()->create();
    $business = Business::factory()->for($owner, 'owner')->create([
        'default_margin_percent' => '30.00',
    ]);
    $material = RawMaterial::factory()->for($business)->create([
        'unit_price' => '1000.0000',
        'wastage_percent' => '0.00',
    ]);

    $productResponse = $this->actingAs($owner)->postJson('/api/products', [
        'name' => 'Roti Tawar',
        'batch_yield' => '10.00',
        'batches_per_month' => '10.00',
        'materials' => [
            ['raw_material_id' => $material->id, 'quantity_used' => '10.0000'],
        ],
    ])->assertCreated();

    $productId = $productResponse->json('id');

    $this->actingAs($owner)->putJson('/api/business', [
        'name' => $business->name,
        'category' => 'kuliner',
        'default_margin_percent' => '50.00',
    ])->assertOk()
        ->assertJsonPath('default_margin_percent', '50.00');

    $this->getJson("/api/products/{$productId}")
        ->assertOk()
        ->assertJsonPath('recommended_price', '2000.00');

    $this->putJson('/api/business', [
        'name' => $business->name,
        'category' => 'kuliner',
        'pricing_method' => 'markup',
        'default_margin_percent' => '50.00',
    ])->assertOk()
        ->assertJsonPath('pricing_method', 'markup')
        ->assertJsonPath('default_margin_percent', '50.00');

    $this->getJson("/api/products/{$productId}")
        ->assertOk()
        ->assertJsonPath('recommended_price', '1500.00');
});

test('markup pricing accepts targets above one hundred percent and calculates from HPP', function () {
    $owner = User::factory()->create();

    $this->actingAs($owner)->postJson('/api/business', [
        'name' => 'Dapur Markup',
        'category' => 'kuliner',
        'pricing_method' => 'markup',
        'default_margin_percent' => '150.00',
    ])->assertCreated()
        ->assertJsonPath('pricing_method', 'markup')
        ->assertJsonPath('default_margin_percent', '150.00');

    $business = $owner->business()->firstOrFail();
    $material = RawMaterial::factory()->for($business)->create([
        'unit_price' => '1000.0000',
        'wastage_percent' => '0.00',
    ]);

    $this->postJson('/api/products', [
        'name' => 'Produk Markup',
        'batch_yield' => '1.00',
        'batches_per_month' => '1.00',
        'materials' => [
            ['raw_material_id' => $material->id, 'quantity_used' => '1.0000'],
        ],
    ])->assertCreated()
        ->assertJsonPath('calculated_hpp', '1000.00')
        ->assertJsonPath('recommended_price', '2500.00');

    $this->assertDatabaseHas('businesses', [
        'id' => $business->id,
        'pricing_method' => 'markup',
        'default_margin_percent' => '150.00',
    ]);
});

test('unauthenticated requests cannot read a business profile', function () {
    $this->getJson('/api/business')->assertUnauthorized();
});

test('an unsupported pricing method is rejected', function () {
    $owner = User::factory()->create();

    $this->actingAs($owner)->postJson('/api/business', [
        'name' => 'Usaha Baru',
        'category' => 'kuliner',
        'pricing_method' => 'discount',
        'default_margin_percent' => '30.00',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['pricing_method']);

    $this->assertDatabaseCount('businesses', 0);
});

test('a default margin of one hundred percent is rejected', function () {
    $owner = User::factory()->create();

    $this->actingAs($owner)->postJson('/api/business', [
        'name' => 'Usaha Baru',
        'category' => 'kuliner',
        'default_margin_percent' => '100.00',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['default_margin_percent']);
});
