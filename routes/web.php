<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/dashboard', function () {
    if (auth()->user()->isAdmin()) {
        return redirect('/admin');
    }

    return redirect('/layanan');
})->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
require __DIR__.'/landing.php';
require __DIR__.'/user_catalog.php';
require __DIR__.'/admin_catalog.php';
require __DIR__.'/admin_transactions.php';
require __DIR__.'/admin_status.php';
