<?php

namespace App\Http\Controllers;

use App\Actions\RecalculateProductCosts;
use App\Http\Requests\OverheadCostRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class OverheadCostController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json(
            $this->businessFor($request)->overheadCosts()->orderBy('name')->get(),
        );
    }

    public function store(OverheadCostRequest $request): JsonResponse
    {
        $overheadCost = $this->businessFor($request)->overheadCosts()->create($request->validated());

        return response()->json($overheadCost, 201);
    }

    public function show(Request $request, int $overheadCost): JsonResponse
    {
        return response()->json($this->businessFor($request)->overheadCosts()->findOrFail($overheadCost));
    }

    public function update(
        OverheadCostRequest $request,
        int $overheadCost,
        RecalculateProductCosts $recalculate,
    ): JsonResponse {
        $business = $this->businessFor($request);
        $cost = $business->overheadCosts()->findOrFail($overheadCost);

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
        int $overheadCost,
        RecalculateProductCosts $recalculate,
    ): JsonResponse|Response {
        $business = $this->businessFor($request);
        $cost = $business->overheadCosts()->findOrFail($overheadCost);
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
