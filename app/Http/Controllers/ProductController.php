<?php

namespace App\Http\Controllers;

use App\Actions\RecalculateProductCosts;
use App\Actions\SaveProductFormula;
use App\Http\Requests\ProductRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json(
            $this->businessFor($request)->products()
                ->with(['materials', 'laborCosts', 'overheadCosts'])
                ->orderBy('name')
                ->get(),
        );
    }

    public function store(
        ProductRequest $request,
        SaveProductFormula $saveProductFormula,
        RecalculateProductCosts $recalculate,
    ): JsonResponse {
        $product = $saveProductFormula->handle(
            $this->businessFor($request),
            $request->validated(),
            $recalculate,
        );

        return response()->json($product, 201);
    }

    public function show(Request $request, int $product): JsonResponse
    {
        return response()->json(
            $this->businessFor($request)->products()
                ->with(['materials', 'laborCosts', 'overheadCosts'])
                ->findOrFail($product),
        );
    }

    public function update(
        ProductRequest $request,
        int $product,
        SaveProductFormula $saveProductFormula,
        RecalculateProductCosts $recalculate,
    ): JsonResponse {
        $business = $this->businessFor($request);
        $existingProduct = $business->products()->findOrFail($product);
        $updatedProduct = $saveProductFormula->handle(
            $business,
            $request->validated(),
            $recalculate,
            $existingProduct,
        );

        return response()->json($updatedProduct);
    }

    public function destroy(
        Request $request,
        int $product,
        RecalculateProductCosts $recalculate,
    ): JsonResponse|Response {
        $business = $this->businessFor($request);
        $existingProduct = $business->products()->findOrFail($product);
        $overheadIds = $existingProduct->overheadCosts()->pluck('overhead_costs.id');

        DB::transaction(function () use ($business, $existingProduct, $overheadIds, $recalculate): void {
            $existingProduct->delete();

            if ($overheadIds->isNotEmpty()) {
                $affectedProducts = $business->products()
                    ->whereHas('overheadCosts', function (Builder $query) use ($overheadIds): void {
                        $query->whereIn('overhead_costs.id', $overheadIds);
                    })
                    ->get();

                foreach ($affectedProducts as $affectedProduct) {
                    $recalculate->handle($affectedProduct);
                }
            }
        });

        return response()->noContent();
    }
}
