<?php

// USR-01 — Landing / pilihan layanan dinamis. Owner: Agent E.

use App\Http\Controllers\LandingController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [LandingController::class, 'index'])->name('home');

Route::get('/privacy', function () {
    return Inertia::render('Privacy');
})->name('privacy');
