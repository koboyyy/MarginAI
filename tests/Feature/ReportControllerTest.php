<?php

use App\Exports\ProductHppExport;
use App\Models\Business;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\LaravelPdf\Facades\Pdf;

uses(RefreshDatabase::class);

test('owner can export products hpp to excel for their business', function () {
    Excel::fake();

    $owner = User::factory()->create();
    $business = Business::factory()->for($owner, 'owner')->create();
    Product::factory()->for($business)->create([
        'name' => 'Produk Milik Bisnis',
        'calculated_hpp' => '12500.00',
    ]);
    Product::factory()->create(['name' => 'Produk Bisnis Lain']);

    $this->actingAs($owner)
        ->get('/api/products/export/excel')
        ->assertOk();

    Excel::assertDownloaded('laporan-hpp-produk.xlsx', function (ProductHppExport $export): bool {
        $rows = $export->collection();

        return $rows->count() === 1
            && $rows->first()[1] === 'Produk Milik Bisnis';
    });
});

test('owner can export products hpp pdf for their business', function () {
    Pdf::fake();

    $owner = User::factory()->create();
    $business = Business::factory()->for($owner, 'owner')->create();
    Product::factory()->for($business)->create([
        'name' => 'Roti Susu',
        'calculated_hpp' => '15000.00',
    ]);

    $this->actingAs($owner)
        ->get('/api/products/export/pdf')
        ->assertOk();

    Pdf::assertRespondedWithPdf(function ($pdf): bool {
        return $pdf->viewName === 'reports.products-hpp'
            && $pdf->viewData['products']->count() === 1
            && $pdf->viewData['products']->first()->name === 'Roti Susu';
    });
});

test('pdf export generates a pdf response', function () {
    $owner = User::factory()->create();
    Business::factory()->for($owner, 'owner')->create();

    $this->actingAs($owner)
        ->get('/api/products/export/pdf')
        ->assertOk()
        ->assertHeader('content-type', 'application/pdf');
});

test('export endpoints require authentication', function () {
    $this->getJson('/api/products/export/excel')->assertUnauthorized();
    $this->getJson('/api/products/export/pdf')->assertUnauthorized();
});
