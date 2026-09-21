<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DocumentationGallery;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Http\JsonResponse;

class LandingController extends Controller
{
    /**
     * Data landing page untuk tamu/mobile home preview.
     */
    public function index(): JsonResponse
    {
        $services = Service::active()
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'description', 'cover_image_url', 'has_sohibul']);

        $products = Product::active()
            ->orderBy('price')
            ->limit(6)
            ->get(['id', 'name', 'slug', 'description', 'price', 'weight_estimate_kg', 'stock', 'primary_image_url']);

        $documentationGalleries = DocumentationGallery::active()
            ->orderBy('order_index')
            ->latest()
            ->get(['id', 'title', 'type', 'file_url', 'youtube_url', 'description', 'category', 'order_index']);

        return response()->json([
            'services' => $services,
            'featured_products' => $products,
            'documentation_galleries' => $documentationGalleries,
        ]);
    }
}
