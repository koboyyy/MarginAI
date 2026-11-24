<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\LaborCostController;
use App\Http\Controllers\OverheadCostController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\PriceSimulationController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RawMaterialController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('api')->as('api.')->group(function (): void {
    Route::prefix('auth')->as('auth.')->group(function (): void {
        Route::post('register', [AuthController::class, 'register'])
            ->middleware('throttle:6,1')
            ->name('register');
        Route::post('login', [AuthController::class, 'login'])
            ->middleware('throttle:login')
            ->name('login');
        Route::post('forgot-password', [PasswordResetController::class, 'sendResetLink'])
            ->middleware('throttle:6,1')
            ->name('password.email');
        Route::post('reset-password', [PasswordResetController::class, 'reset'])
            ->middleware('throttle:6,1')
            ->name('password.update');

        Route::middleware('auth:web')->group(function (): void {
            Route::get('me', [AuthController::class, 'current'])->name('me');
            Route::post('logout', [AuthController::class, 'logout'])->name('logout');
            Route::post('email/verification-notification', [EmailVerificationController::class, 'send'])
                ->middleware('throttle:6,1')
                ->name('verification.send');
        });
    });

    Route::middleware(['auth:web', 'verified'])->group(function (): void {
        Route::get('dashboard', [DashboardController::class, 'show'])->name('dashboard.show');
        Route::get('business', [BusinessController::class, 'show'])->name('business.show');
        Route::post('business', [BusinessController::class, 'store'])->name('business.store');
        Route::put('business', [BusinessController::class, 'update'])->name('business.update');

        Route::apiResource('raw-materials', RawMaterialController::class);
        Route::apiResource('labor-costs', LaborCostController::class);
        Route::apiResource('overhead-costs', OverheadCostController::class);
        Route::apiResource('products', ProductController::class);
        Route::get('products/export/pdf', [ReportController::class, 'productsPdf'])
            ->name('products.export.pdf');
        Route::get('products/export/excel', [ReportController::class, 'productsExcel'])
            ->name('products.export.excel');
        Route::get('products/{product}/price-simulations', [PriceSimulationController::class, 'index'])
            ->name('products.price-simulations.index');
        Route::post('products/{product}/price-simulations', [PriceSimulationController::class, 'store'])
            ->name('products.price-simulations.store');
    });
});

Route::get('api/auth/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->middleware(['auth:web', 'signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::get('api/auth/password/reset/{token}', [PasswordResetController::class, 'showResetForm'])
    ->name('password.reset');
