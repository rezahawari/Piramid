<?php

// Webhook Midtrans — unauthenticated, signature-verified, CSRF-exempt.

use App\Http\Controllers\Webhooks\MidtransWebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/webhooks/midtrans/notification', [MidtransWebhookController::class, 'handle'])
    ->name('webhooks.midtrans');
