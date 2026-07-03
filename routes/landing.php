<?php

// USR-01 — Landing / pilihan layanan dinamis. Owner: Agent E.

use App\Http\Controllers\LandingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'index'])->name('home');
