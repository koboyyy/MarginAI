<?php

namespace App\Http\Controllers;

use App\Actions\GetDashboardData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function show(Request $request, GetDashboardData $getDashboardData): JsonResponse
    {
        return response()->json(
            $getDashboardData->handle($this->businessFor($request)),
        );
    }
}
