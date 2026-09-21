<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Http\JsonResponse;

class CatalogController extends Controller
{
    /**
     * USR-02 — Daftar semua layanan publik beserta produk hewan yang tersedia.
     */
    public function services(): JsonResponse
    {
        $services = Service::where('is_active', true)
            ->with(['products' => function ($query) {
                $query->active()
                    ->orderBy('products.name')
                    ->select([
                        'products.id',
                        'products.name',
                        'products.slug',
                        'products.description',
                        'products.price',
                        'products.weight_estimate_kg',
                        'products.stock',
                        'products.max_sohibul',
                        'products.primary_image_url',
                    ]);
            }])
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'description', 'cover_image_url', 'has_sohibul']);

        return response()->json([
            'data' => $services,
        ]);
    }

    /**
     * USR-02 — Detail layanan beserta daftar produk aktif miliknya.
     */
    public function showService(Service $service): JsonResponse
    {
        if (! $service->is_active) {
            return response()->json(['message' => 'Layanan tidak ditemukan atau tidak aktif.'], 404);
        }

        $products = $service->products()
            ->active()
            ->orderBy('products.name')
            ->get([
                'products.id',
                'products.name',
                'products.slug',
                'products.description',
                'products.price',
                'products.weight_estimate_kg',
                'products.stock',
                'products.max_sohibul',
                'products.primary_image_url',
            ]);

        $otherServices = Service::where('is_active', true)
            ->where('id', '!=', $service->id)
            ->get(['id', 'name', 'slug', 'cover_image_url']);

        return response()->json([
            'service' => $service->only(['id', 'name', 'slug', 'description', 'cover_image_url', 'has_sohibul']),
            'products' => $products,
            'other_services' => $otherServices,
        ]);
    }

    /**
     * USR-02 — Detail produk dalam konteks layanan.
     */
    public function showProduct(Service $service, Product $product): JsonResponse
    {
        if (! $service->is_active || ! $product->is_active) {
            return response()->json(['message' => 'Produk tidak ditemukan atau tidak aktif.'], 404);
        }

        return response()->json([
            'service' => $service->only(['id', 'name', 'slug', 'has_sohibul']),
            'product' => $product->only([
                'id',
                'name',
                'slug',
                'description',
                'price',
                'weight_estimate_kg',
                'stock',
                'max_sohibul',
                'primary_image_url',
                'gallery',
            ]),
        ]);
    }
}
