<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Service;
use Inertia\Inertia;
use Inertia\Response;

class LandingController extends Controller
{
    /**
     * Display the landing page with the active services and featured products.
     */
    public function index(): Response
    {
        return Inertia::render('Landing', [
            'services' => Service::active()
                ->orderBy('name')
                ->get(['id', 'name', 'slug', 'description', 'cover_image_url']),
            'products' => Product::active()
                ->orderBy('price')
                ->limit(3)
                ->get(['id', 'name', 'slug', 'price', 'weight_estimate_kg', 'primary_image_url']),
        ]);
    }
}
