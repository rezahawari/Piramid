<?php

// ADM-04 — Manajemen status & dokumentasi.

use App\Http\Controllers\Admin\TransactionDocumentationController;
use App\Http\Controllers\Admin\TransactionStatusController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/transaksi/{transaction:transaction_code}', [TransactionStatusController::class, 'show'])
            ->name('transactions.show');
        Route::post('/transaksi/{transaction:transaction_code}/status', [TransactionStatusController::class, 'update'])
            ->name('transactions.status');
        Route::post('/transaksi/{transaction:transaction_code}/dokumentasi', [TransactionDocumentationController::class, 'store'])
            ->name('transactions.documentation.store');
        Route::delete('/dokumentasi/{documentation}', [TransactionDocumentationController::class, 'destroy'])
            ->name('documentation.destroy');
    });
