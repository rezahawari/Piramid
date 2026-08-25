<?php

namespace App\Http\Controllers;

use App\Models\DocumentationGallery;
use App\Models\Product;
use App\Models\Service;
use Inertia\Inertia;
use Inertia\Response;

class LandingController extends Controller
{
    /**
     * Display the landing page with the active services, featured products, and documentation gallery.
     */
    public function index(): \Symfony\Component\HttpFoundation\Response
    {
        // Jika user sudah login, redirect langsung sesuai role (Admin -> /admin, User -> /layanan)
        if (auth()->check()) {
            return auth()->user()->isAdmin()
                ? redirect()->route('admin.dashboard')
                : redirect()->route('services.index');
        }

        // Jika diakses dari PWA / Android APK (query param source=pwa atau header display mode standalone)
        if (request()->query('source') === 'pwa' || request()->header('X-PWA-App') === 'true') {
            return redirect()->route('login');
        }

        return Inertia::render('Landing', [
            'services' => Service::active()
                ->orderBy('name')
                ->get(['id', 'name', 'slug', 'description', 'cover_image_url']),
            'products' => Product::active()
                ->orderBy('price')
                ->limit(3)
                ->get(['id', 'name', 'slug', 'price', 'weight_estimate_kg', 'primary_image_url']),
            'documentationGalleries' => DocumentationGallery::active()
                ->orderBy('order_index')
                ->latest()
                ->get(),
        ]);
    }
}
