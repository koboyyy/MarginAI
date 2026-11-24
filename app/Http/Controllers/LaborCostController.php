<?php

namespace App\Http\Controllers;

use App\Actions\RecalculateProductCosts;
use App\Http\Requests\LaborCostRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class LaborCostController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json(
            $this->businessFor($request)->laborCosts()->orderBy('name')->get(),
        );
    }

    public function store(LaborCostRequest $request): JsonResponse
    {
        $laborCost = $this->businessFor($request)->laborCosts()->create($request->validated());

        return response()->json($laborCost, 201);
    }

    public function show(Request $request, int $laborCost): JsonResponse
    {
        return response()->json($this->businessFor($request)->laborCosts()->findOrFail($laborCost));
    }

    public function update(
        LaborCostRequest $request,
        int $laborCost,
        RecalculateProductCosts $recalculate,
    ): JsonResponse {
        $business = $this->businessFor($request);
        $cost = $business->laborCosts()->findOrFail($laborCost);

        DB::transaction(function () use ($cost, $request, $recalculate): void {
            $cost->update($request->validated());

            foreach ($cost->products()->get() as $product) {
                $recalculate->handle($product);
            }
        });

        return response()->json($cost->refresh());
    }

    public function destroy(
        Request $request,
        int $laborCost,
        RecalculateProductCosts $recalculate,
    ): JsonResponse|Response {
        $business = $this->businessFor($request);
        $cost = $business->laborCosts()->findOrFail($laborCost);
        $productIds = $cost->products()->pluck('products.id');

        DB::transaction(function () use ($business, $cost, $productIds, $recalculate): void {
            $cost->delete();

            foreach ($productIds as $productId) {
                $recalculate->handle($business->products()->findOrFail($productId));
            }
        });

        return response()->noContent();
    }
}
