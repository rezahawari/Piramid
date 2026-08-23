<?php

// USR-02/03/04/05 — Katalog, checkout, pembayaran, pelacakan transaksi. Owner: Agent B (+ Agent C utk PaymentController, Agent D utk ManualTransferController).

use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ManualTransferController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

// Katalog publik (USR-02)
Route::get('/layanan', [CatalogController::class, 'services'])->name('services.index');
Route::get('/layanan/{service:slug}', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/layanan/{service:slug}/produk/{product:slug}', [CatalogController::class, 'show'])->name('catalog.show');

Route::middleware('auth')->group(function () {
    // Checkout (USR-03)
    Route::get('/layanan/{service:slug}/produk/{product:slug}/checkout', [CheckoutController::class, 'create'])->name('checkout.create');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    // Pelacakan transaksi (USR-05)
    Route::get('/transaksi', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transaksi/{transaction:transaction_code}', [TransactionController::class, 'show'])->name('transactions.show');

    // Pembayaran (USR-04)
    Route::post('/pembayaran/{transaction:transaction_code}/snap-token', [PaymentController::class, 'snapToken'])
        ->name('payment.snap-token');
    Route::post('/pembayaran/{transaction:transaction_code}/bukti-transfer', [ManualTransferController::class, 'store'])
        ->name('payment.manual-proof');
});
