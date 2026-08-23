<?php

// ADM-01/02 — CRUD layanan master, inventaris & pointing. Owner: Agent A.

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ServiceController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('layanan', ServiceController::class)
            ->parameters(['layanan' => 'layanan'])
            ->except(['show']);

        Route::resource('produk', ProductController::class)
            ->parameters(['produk' => 'produk'])
            ->except(['show']);
    });
