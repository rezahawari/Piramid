<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class LandingController extends Controller
{
    /**
     * Display the landing page with the active services.
     */
    public function index(): Response
    {
        return Inertia::render('Landing', [
            'services' => Service::active()
                ->orderBy('name')
                ->get(['id', 'name', 'slug', 'description', 'cover_image_url']),
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
        ]);
    }
}
