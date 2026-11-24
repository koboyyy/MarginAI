<?php

namespace App\Exports;

use App\Models\Business;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductHppExport implements FromCollection, WithHeadings
{
    public function __construct(private readonly Business $business) {}

    public function collection(): Collection
    {
        return $this->business->products()
            ->orderBy('name')
            ->get()
            ->map(fn ($product): array => [
                $product->sku,
                $product->name,
                $product->batch_yield,
                $product->calculated_hpp,
                $product->recommended_price,
                $product->selling_price,
            ]);
    }

    public function headings(): array
    {
        return [
            'SKU',
            'Nama Produk',
            'Yield Batch',
            'HPP per Unit',
            'Harga Rekomendasi',
            'Harga Jual',
        ];
    }
}
