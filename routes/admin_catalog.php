<?php

// ADM-01/02 — CRUD layanan master, inventaris & pointing. Owner: Agent A.

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Manajemen Layanan
        Route::resource('layanan', ServiceController::class)
            ->parameters(['layanan' => 'layanan'])
            ->except(['show']);

        // Manajemen Produk Hewan
        Route::resource('produk', ProductController::class)
            ->parameters(['produk' => 'produk'])
            ->except(['show']);

        // Manajemen Pengguna (CRUD, Soft Delete, Status, Reset Password)
        Route::resource('users', UserController::class)
            ->parameters(['users' => 'user'])
            ->except(['show']);
        Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])
            ->name('users.toggle-status');
        Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])
            ->name('users.reset-password');
        Route::post('/users/{user}/restore', [UserController::class, 'restore'])
            ->name('users.restore');
        Route::delete('/users/{user}/force-delete', [UserController::class, 'forceDelete'])
            ->name('users.force-delete');
    });
