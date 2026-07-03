<?php

// ADM-01/02 — CRUD layanan master, inventaris & pointing. Owner: Agent A.

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ServiceController;
use App\Models\Product;
use App\Models\Service;
use App\Models\Transaction;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', function () {
            return Inertia::render('Admin/Dashboard', [
                'stats' => [
                    'services' => Service::count(),
                    'products' => Product::count(),
                    'transactions' => Transaction::count(),
                    'pendingManualPayments' => Transaction::query()
                        ->where('payment_method', PaymentMethod::ManualTransfer->value)
                        ->where('payment_status', PaymentStatus::Pending->value)
                        ->count(),
                ],
            ]);
        })->name('dashboard');

        Route::resource('layanan', ServiceController::class)
            ->parameters(['layanan' => 'layanan'])
            ->except(['show']);

        Route::resource('produk', ProductController::class)
            ->parameters(['produk' => 'produk'])
            ->except(['show']);
    });
