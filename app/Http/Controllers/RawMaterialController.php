<?php

namespace App\Http\Controllers;

use App\Actions\RecalculateProductCosts;
use App\Http\Requests\RawMaterialRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class RawMaterialController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json(
            $this->businessFor($request)->rawMaterials()->orderBy('name')->get(),
        );
    }

    public function store(RawMaterialRequest $request): JsonResponse
    {
        $rawMaterial = $this->businessFor($request)->rawMaterials()->create($request->validated());

        return response()->json($rawMaterial, 201);
    }

    public function show(Request $request, int $rawMaterial): JsonResponse
    {
        return response()->json($this->businessFor($request)->rawMaterials()->findOrFail($rawMaterial));
    }

    public function update(
        RawMaterialRequest $request,
        int $rawMaterial,
        RecalculateProductCosts $recalculate,
    ): JsonResponse {
        $business = $this->businessFor($request);
        $material = $business->rawMaterials()->findOrFail($rawMaterial);

        DB::transaction(function () use ($material, $request, $recalculate): void {
            $material->update($request->validated());

            foreach ($material->products()->get() as $product) {
                $recalculate->handle($product);
            }
        });

        return response()->json($material->refresh());
    }

    public function destroy(Request $request, int $rawMaterial): JsonResponse|Response
    {
        $material = $this->businessFor($request)->rawMaterials()->findOrFail($rawMaterial);

        if ($material->products()->exists()) {
            return response()->json([
                'message' => 'Bahan baku yang digunakan dalam formula produk tidak dapat dihapus.',
            ], 409);
        }

        $material->delete();

        return response()->noContent();
    }
}
