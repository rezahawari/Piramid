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
            ->get(['id', 'name', 'slug', 'description', 'cover_image_url', 'has_sohibul'])
            ->map(function ($s) {
                return [
                    'id' => $s->id,
                    'name' => $s->name,
                    'slug' => $s->slug,
                    'description' => $s->description,
                    'cover_image_url' => LandingController::formatMediaUrl($s->cover_image_url),
                    'has_sohibul' => $s->has_sohibul,
                    'products' => $s->products->map(function ($p) {
                        return [
                            'id' => $p->id,
                            'name' => $p->name,
                            'slug' => $p->slug,
                            'description' => $p->description,
                            'price' => $p->price,
                            'weight_estimate_kg' => $p->weight_estimate_kg,
                            'stock' => $p->stock,
                            'max_sohibul' => $p->max_sohibul,
                            'primary_image_url' => LandingController::formatMediaUrl($p->primary_image_url),
                        ];
                    }),
                ];
            });

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
            ])
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'slug' => $p->slug,
                    'description' => $p->description,
                    'price' => $p->price,
                    'weight_estimate_kg' => $p->weight_estimate_kg,
                    'stock' => $p->stock,
                    'max_sohibul' => $p->max_sohibul,
                    'primary_image_url' => LandingController::formatMediaUrl($p->primary_image_url),
                ];
            });

        $otherServices = Service::where('is_active', true)
            ->where('id', '!=', $service->id)
            ->get(['id', 'name', 'slug', 'cover_image_url'])
            ->map(function ($s) {
                return [
                    'id' => $s->id,
                    'name' => $s->name,
                    'slug' => $s->slug,
                    'cover_image_url' => LandingController::formatMediaUrl($s->cover_image_url),
                ];
            });

        return response()->json([
            'service' => [
                'id' => $service->id,
                'name' => $service->name,
                'slug' => $service->slug,
                'description' => $service->description,
                'cover_image_url' => LandingController::formatMediaUrl($service->cover_image_url),
                'has_sohibul' => $service->has_sohibul,
            ],
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

        $gallery = collect($product->gallery ?? [])
            ->map(fn ($img) => LandingController::formatMediaUrl($img))
            ->values();

        return response()->json([
            'service' => [
                'id' => $service->id,
                'name' => $service->name,
                'slug' => $service->slug,
                'has_sohibul' => $service->has_sohibul,
            ],
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'description' => $product->description,
                'price' => $product->price,
                'weight_estimate_kg' => $product->weight_estimate_kg,
                'stock' => $product->stock,
                'max_sohibul' => $product->max_sohibul,
                'primary_image_url' => LandingController::formatMediaUrl($product->primary_image_url),
                'gallery' => $gallery,
            ],
        ]);
    }
}
