<?php

// ADM-03 — Manajemen transaksi & validasi pembayaran manual.

use App\Http\Controllers\Admin\TransactionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/transaksi', [TransactionController::class, 'index'])
            ->name('transactions.index');
        Route::delete('/transaksi/{transaction:transaction_code}', [TransactionController::class, 'destroy'])
            ->name('transactions.destroy');
        Route::post('/transaksi/{transaction:transaction_code}/setujui', [TransactionController::class, 'approve'])
            ->name('transactions.approve');
        Route::post('/transaksi/{transaction:transaction_code}/tolak', [TransactionController::class, 'reject'])
            ->name('transactions.reject');
    });
