<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CatalogController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\LandingController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\PushSubscriptionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes for Piramid Mobile & Client Apps
|--------------------------------------------------------------------------
| Prefix: /api/v1 (atau /api)
| Authentication: Laravel Sanctum Bearer Token
*/

Route::prefix('v1')->group(function () {
    // -------------------------------------------------------------
    // 1. Publik / Guest Routes
    // -------------------------------------------------------------
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/auth/reset-password', [AuthController::class, 'resetPassword']);

    // Onboarding Heroes (Dua alias endpoint sesuai spesifikasi)
    Route::get('/landing/heroes', [LandingController::class, 'heroes']);
    Route::get('/onboarding', [LandingController::class, 'heroes']);

    // Landing & Catalog Discovery
    Route::get('/landing', [LandingController::class, 'index']);
    Route::get('/services', [CatalogController::class, 'services']);
    Route::get('/services/{service:slug}', [CatalogController::class, 'showService']);
    Route::get('/services/{service:slug}/products/{product:slug}', [CatalogController::class, 'showProduct']);

    // -------------------------------------------------------------
    // 2. Protected Routes (Harus Login / Bearer Token)
    // -------------------------------------------------------------
    Route::middleware('auth:sanctum')->group(function () {
        // Auth Management
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        // Profile
        Route::get('/profile', [ProfileController::class, 'show']);
        Route::patch('/profile', [ProfileController::class, 'update']);
        Route::put('/profile/password', [ProfileController::class, 'updatePassword']);
        Route::delete('/profile', [ProfileController::class, 'destroy']);

        // Checkout
        Route::get('/checkout/options', [CheckoutController::class, 'options']);
        Route::post('/checkout', [CheckoutController::class, 'store']);

        // Pelacakan Transaksi
        Route::get('/transactions', [TransactionController::class, 'index']);
        Route::get('/transactions/{transaction:transaction_code}', [TransactionController::class, 'show']);

        // Pembayaran
        Route::post('/transactions/{transaction:transaction_code}/snap-token', [PaymentController::class, 'snapToken']);
        Route::post('/transactions/{transaction:transaction_code}/manual-transfer-proof', [PaymentController::class, 'uploadManualProof']);

        // Push Notifications
        Route::post('/push-subscribe', [PushSubscriptionController::class, 'subscribe']);
    });
});

// Explicit CORS Media Route fallback untuk localhost/mobile browser
Route::get('/media/{path}', [MediaController::class, 'serve'])->where('path', '.*');
