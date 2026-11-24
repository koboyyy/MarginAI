<?php

namespace App\Http\Controllers;

use App\Actions\SimulateProductPrice;
use App\Http\Requests\StorePriceSimulationRequest;
use App\Jobs\AnalyzePriceSimulation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PriceSimulationController extends Controller
{
    public function index(Request $request, int $product): JsonResponse
    {
        $existingProduct = $this->businessFor($request)->products()->findOrFail($product);

        return response()->json(
            $existingProduct->priceSimulations()
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->get(),
        );
    }

    public function store(
        StorePriceSimulationRequest $request,
        int $product,
        SimulateProductPrice $simulate,
    ): JsonResponse {
        $existingProduct = $this->businessFor($request)->products()->findOrFail($product);
        $simulation = $simulate->handle($existingProduct, $request->validated());

        AnalyzePriceSimulation::dispatch($simulation->id)->afterCommit();

        return response()->json($simulation, 201);
    }
}
