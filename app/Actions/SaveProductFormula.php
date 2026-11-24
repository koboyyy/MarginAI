<?php

namespace App\Actions;

use App\Models\Business;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class SaveProductFormula
{
    /**
     * @param  array{
     *     name: string,
     *     sku?: string|null,
     *     batch_yield: numeric-string|int|float,
     *     batches_per_month: numeric-string|int|float,
     *     selling_price?: numeric-string|int|float|null,
     *     materials?: array<int, array{raw_material_id: int, quantity_used: numeric-string|int|float}>,
     *     labor_costs?: array<int, array{labor_cost_id: int, quantity_used: numeric-string|int|float}>,
     *     overhead_costs?: array<int, array{overhead_cost_id: int}>
     * }  $data
     */
    public function handle(
        Business $business,
        array $data,
        RecalculateProductCosts $recalculate,
        ?Product $product = null,
    ): Product {
        return DB::transaction(function () use ($business, $data, $recalculate, $product): Product {
            $product ??= new Product;

            if ($product->exists && $product->business_id !== $business->id) {
                abort(404);
            }

            $isNewProduct = ! $product->exists;
            $previousOverheadIds = $product->exists
                ? $product->overheadCosts()->pluck('overhead_costs.id')->all()
                : [];

            $product->fill(Arr::only($data, [
                'name',
                'sku',
                'batch_yield',
                'batches_per_month',
                'selling_price',
            ]));
            $product->business()->associate($business);
            $product->save();

            $materialSync = [];

            foreach ($data['materials'] ?? [] as $materialData) {
                $business->rawMaterials()->findOrFail($materialData['raw_material_id']);
                $materialSync[$materialData['raw_material_id']] = [
                    'quantity_used' => (string) $materialData['quantity_used'],
                    'subtotal_cost' => '0.0000',
                ];
            }

            $laborSync = [];

            foreach ($data['labor_costs'] ?? [] as $laborData) {
                $business->laborCosts()->findOrFail($laborData['labor_cost_id']);
                $laborSync[$laborData['labor_cost_id']] = [
                    'quantity_used' => (string) $laborData['quantity_used'],
                    'subtotal_cost' => '0.0000',
                ];
            }

            $overheadSync = [];

            foreach ($data['overhead_costs'] ?? [] as $overheadData) {
                $business->overheadCosts()->findOrFail($overheadData['overhead_cost_id']);
                $overheadSync[$overheadData['overhead_cost_id']] = [
                    'subtotal_cost' => '0.0000',
                ];
            }

            if (array_key_exists('materials', $data) || $isNewProduct) {
                $product->materials()->sync($materialSync);
            }

            if (array_key_exists('labor_costs', $data) || $isNewProduct) {
                $product->laborCosts()->sync($laborSync);
            }

            if (array_key_exists('overhead_costs', $data) || $isNewProduct) {
                $product->overheadCosts()->sync($overheadSync);
            }

            $overheadIds = array_values(array_unique(array_merge(
                $previousOverheadIds,
                array_keys($overheadSync),
            )));
            $affectedProductIds = [$product->id];

            if ($overheadIds !== []) {
                $affectedProductIds = array_merge(
                    $affectedProductIds,
                    $business->products()
                        ->whereHas('overheadCosts', function (Builder $query) use ($overheadIds): void {
                            $query->whereIn('overhead_costs.id', $overheadIds);
                        })
                        ->pluck('products.id')
                        ->all(),
                );
            }

            foreach (array_unique($affectedProductIds) as $affectedProductId) {
                $recalculate->handle($business->products()->findOrFail($affectedProductId));
            }

            return $product->refresh()->load([
                'materials',
                'laborCosts',
                'overheadCosts',
            ]);
        });
    }
}
