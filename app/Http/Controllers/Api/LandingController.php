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
            ->get(['id', 'name', 'slug', 'description', 'cover_image_url', 'has_sohibul'])
            ->map(function ($s) {
                return [
                    'id' => $s->id,
                    'name' => $s->name,
                    'slug' => $s->slug,
                    'description' => $s->description,
                    'cover_image_url' => self::formatMediaUrl($s->cover_image_url),
                    'has_sohibul' => $s->has_sohibul,
                ];
            });

        $products = Product::active()
            ->orderBy('price')
            ->limit(6)
            ->get(['id', 'name', 'slug', 'description', 'price', 'weight_estimate_kg', 'stock', 'primary_image_url'])
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'slug' => $p->slug,
                    'description' => $p->description,
                    'price' => $p->price,
                    'weight_estimate_kg' => $p->weight_estimate_kg,
                    'stock' => $p->stock,
                    'primary_image_url' => self::formatMediaUrl($p->primary_image_url),
                ];
            });

        $documentationGalleries = DocumentationGallery::active()
            ->orderBy('order_index')
            ->latest()
            ->get(['id', 'title', 'type', 'file_url', 'youtube_url', 'description', 'category', 'order_index'])
            ->map(function ($doc) {
                return [
                    'id' => $doc->id,
                    'title' => $doc->title,
                    'type' => $doc->type,
                    'file_url' => self::formatMediaUrl($doc->file_url),
                    'youtube_url' => $doc->youtube_url,
                    'description' => $doc->description,
                    'category' => $doc->category,
                    'order_index' => $doc->order_index,
                ];
            });

        return response()->json([
            'services' => $services,
            'featured_products' => $products,
            'documentation_galleries' => $documentationGalleries,
        ]);
    }

    /**
     * Pastikan semua path asset lokal menjadi absolute URL dengan domain backend.
     */
    public static function formatMediaUrl(?string $url): ?string
    {
        if (blank($url)) {
            return null;
        }

        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return $url;
        }

        return url($url);
    }
}
