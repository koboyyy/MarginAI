<?php

namespace App\Actions;

use App\Enums\OverheadCostType;
use App\Enums\PricingMethod;
use App\Models\Product;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;

class RecalculateProductCosts
{
    public function handle(Product $product): Product
    {
        $product->load(['materials', 'laborCosts', 'overheadCosts.products']);

        $materialTotal = BigDecimal::zero();

        foreach ($product->materials as $material) {
            $wastageMultiplier = BigDecimal::of('1')->plus(
                BigDecimal::of((string) $material->wastage_percent)
                    ->dividedBy('100', 8, RoundingMode::HalfUp),
            );
            $subtotal = BigDecimal::of((string) $material->pivot->quantity_used)
                ->multipliedBy((string) $material->unit_price)
                ->multipliedBy($wastageMultiplier)
                ->toScale(4, RoundingMode::HalfUp);

            $product->materials()->updateExistingPivot($material->id, [
                'subtotal_cost' => $subtotal->toString(),
            ]);
            $materialTotal = $materialTotal->plus($subtotal);
        }

        $laborTotal = BigDecimal::zero();

        foreach ($product->laborCosts as $laborCost) {
            $subtotal = BigDecimal::of((string) $laborCost->rate)
                ->multipliedBy((string) $laborCost->pivot->quantity_used)
                ->toScale(4, RoundingMode::HalfUp);

            $product->laborCosts()->updateExistingPivot($laborCost->id, [
                'subtotal_cost' => $subtotal->toString(),
            ]);
            $laborTotal = $laborTotal->plus($subtotal);
        }

        $overheadTotal = BigDecimal::zero();

        foreach ($product->overheadCosts as $overheadCost) {
            $amount = BigDecimal::of((string) $overheadCost->amount);

            if ($overheadCost->type === OverheadCostType::Fixed) {
                $monthlyBatchCount = BigDecimal::zero();

                foreach ($overheadCost->products as $allocatedProduct) {
                    $monthlyBatchCount = $monthlyBatchCount->plus((string) $allocatedProduct->batches_per_month);
                }

                $amount = $amount->dividedBy(
                    $monthlyBatchCount,
                    4,
                    RoundingMode::HalfUp,
                );
            }

            $subtotal = $amount->toScale(4, RoundingMode::HalfUp);
            $product->overheadCosts()->updateExistingPivot($overheadCost->id, [
                'subtotal_cost' => $subtotal->toString(),
            ]);
            $overheadTotal = $overheadTotal->plus($subtotal);
        }

        $business = $product->business()->firstOrFail();
        $batchCost = $materialTotal->plus($laborTotal)->plus($overheadTotal);
        $hpp = $batchCost->dividedBy($product->batch_yield, 2, RoundingMode::HalfUp);
        $targetPercent = BigDecimal::of((string) $business->default_margin_percent);

        if ($business->pricing_method === PricingMethod::Markup) {
            $recommendedPrice = $hpp
                ->multipliedBy(BigDecimal::of('100')->plus($targetPercent))
                ->dividedBy('100', 2, RoundingMode::HalfUp);
        } else {
            $remainingMargin = BigDecimal::of('100')->minus($targetPercent);
            $recommendedPrice = $hpp
                ->multipliedBy('100')
                ->dividedBy($remainingMargin, 2, RoundingMode::HalfUp);
        }

        $product->forceFill([
            'labor_cost_per_batch' => $laborTotal->toScale(4, RoundingMode::HalfUp)->toString(),
            'overhead_cost_per_batch' => $overheadTotal->toScale(4, RoundingMode::HalfUp)->toString(),
            'calculated_hpp' => $hpp->toString(),
            'recommended_price' => $recommendedPrice->toString(),
        ])->save();

        return $product->refresh();
    }
}
