<?php

namespace App\Http\Controllers;

use App\Actions\RecalculateProductCosts;
use App\Http\Requests\StoreBusinessRequest;
use App\Http\Requests\UpdateBusinessRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BusinessController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        return response()->json($this->businessFor($request));
    }

    public function store(StoreBusinessRequest $request): JsonResponse
    {
        $owner = $this->owner($request);

        if ($owner->business()->exists()) {
            return response()->json(['message' => 'Profil usaha sudah tersedia.'], 409);
        }

        $business = $owner->business()->create($request->validated());

        return response()->json($business, 201);
    }

    public function update(
        UpdateBusinessRequest $request,
        RecalculateProductCosts $recalculate,
    ): JsonResponse {
        $business = $this->businessFor($request);

        DB::transaction(function () use ($business, $request, $recalculate): void {
            $business->update($request->validated());

            foreach ($business->products()->get() as $product) {
                $recalculate->handle($product);
            }
        });

        return response()->json($business->refresh());
    }
}
