<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DistributionOption;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    /**
     * USR-02 — Daftar semua layanan publik beserta produk hewan & variannya.
     */
    public function services(Request $request): JsonResponse
    {
        $locale = $request->get('lang', 'id');

        $services = Service::where('is_active', true)
            ->with(['products' => function ($query) {
                $query->active()
                    ->orderBy('products.name')
                    ->with('activeVariants');
            }])
            ->orderBy('name')
            ->get()
            ->map(function ($s) use ($locale) {
                return [
                    'id' => $s->id,
                    'name' => $s->name,
                    'slug' => $s->slug,
                    'description' => $s->description,
                    'cover_image_url' => LandingController::formatMediaUrl($s->cover_image_url),
                    'has_sohibul' => $s->has_sohibul,
                    'has_cooking_option' => $s->has_cooking_option,
                    'default_cooking_option' => $s->default_cooking_option,
                    'cooking_fee' => [
                        'idr' => (float) $s->cooking_fee_idr,
                        'usd' => (float) $s->cooking_fee_usd,
                        'cny' => (float) $s->cooking_fee_cny,
                        'sar' => (float) $s->cooking_fee_sar,
                    ],
                    'products' => $s->products->map(function ($p) use ($locale) {
                        return [
                            'id' => $p->id,
                            'name' => $p->name,
                            'slug' => $p->slug,
                            'description' => $p->description,
                            'prices' => [
                                'idr' => (float) $p->price,
                                'usd' => (float) ($p->price_usd ?? 0),
                                'cny' => (float) ($p->price_cny ?? 0),
                                'sar' => (float) ($p->price_sar ?? 0),
                            ],
                            'weight_estimate_kg' => (float) $p->weight_estimate_kg,
                            'stock' => (int) $p->stock,
                            'max_sohibul' => (int) $p->max_sohibul,
                            'primary_image_url' => LandingController::formatMediaUrl($p->primary_image_url),
                            'variants' => $p->activeVariants->map(function ($v) use ($locale) {
                                return [
                                    'id' => $v->id,
                                    'name' => $v->getLocalizedName($locale),
                                    'spec_description' => $v->spec_description,
                                    'prices' => [
                                        'idr' => (float) $v->price_idr,
                                        'usd' => (float) ($v->price_usd ?? 0),
                                        'cny' => (float) ($v->price_cny ?? 0),
                                        'sar' => (float) ($v->price_sar ?? 0),
                                    ],
                                    'stock' => (int) $v->stock,
                                ];
                            }),
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
    public function showService(Request $request, Service $service): JsonResponse
    {
        if (! $service->is_active) {
            return response()->json(['message' => 'Layanan tidak ditemukan atau tidak aktif.'], 404);
        }

        $locale = $request->get('lang', 'id');

        $products = $service->products()
            ->active()
            ->orderBy('products.name')
            ->with('activeVariants')
            ->get()
            ->map(function ($p) use ($locale) {
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'slug' => $p->slug,
                    'description' => $p->description,
                    'prices' => [
                        'idr' => (float) $p->price,
                        'usd' => (float) ($p->price_usd ?? 0),
                        'cny' => (float) ($p->price_cny ?? 0),
                        'sar' => (float) ($p->price_sar ?? 0),
                    ],
                    'weight_estimate_kg' => (float) $p->weight_estimate_kg,
                    'stock' => (int) $p->stock,
                    'max_sohibul' => (int) $p->max_sohibul,
                    'primary_image_url' => LandingController::formatMediaUrl($p->primary_image_url),
                    'variants' => $p->activeVariants->map(function ($v) use ($locale) {
                        return [
                            'id' => $v->id,
                            'name' => $v->getLocalizedName($locale),
                            'spec_description' => $v->spec_description,
                            'prices' => [
                                'idr' => (float) $v->price_idr,
                                'usd' => (float) ($v->price_usd ?? 0),
                                'cny' => (float) ($v->price_cny ?? 0),
                                'sar' => (float) ($v->price_sar ?? 0),
                            ],
                            'stock' => (int) $v->stock,
                        ];
                    }),
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
                'has_cooking_option' => $service->has_cooking_option,
                'default_cooking_option' => $service->default_cooking_option,
                'cooking_fee' => [
                    'idr' => (float) $service->cooking_fee_idr,
                    'usd' => (float) $service->cooking_fee_usd,
                    'cny' => (float) $service->cooking_fee_cny,
                    'sar' => (float) $service->cooking_fee_sar,
                ],
            ],
            'products' => $products,
            'other_services' => $otherServices,
        ]);
    }

    /**
     * USR-02 — Detail produk dalam konteks layanan.
     */
    public function showProduct(Request $request, Service $service, Product $product): JsonResponse
    {
        if (! $service->is_active || ! $product->is_active) {
            return response()->json(['message' => 'Produk tidak ditemukan atau tidak aktif.'], 404);
        }

        $locale = $request->get('lang', 'id');

        $gallery = collect($product->gallery ?? [])
            ->map(fn ($img) => LandingController::formatMediaUrl($img))
            ->values();

        $variants = $product->activeVariants->map(function ($v) use ($locale) {
            return [
                'id' => $v->id,
                'name' => $v->getLocalizedName($locale),
                'spec_description' => $v->spec_description,
                'prices' => [
                    'idr' => (float) $v->price_idr,
                    'usd' => (float) ($v->price_usd ?? 0),
                    'cny' => (float) ($v->price_cny ?? 0),
                    'sar' => (float) ($v->price_sar ?? 0),
                ],
                'stock' => (int) $v->stock,
            ];
        });

        // Ambil opsi distribusi yang aktif
        $distributions = DistributionOption::active()->get()->map(function ($d) use ($locale) {
            return [
                'id' => $d->id,
                'name' => $d->getLocalizedName($locale),
                'description' => $d->getLocalizedDescription($locale),
                'fees' => [
                    'idr' => (float) $d->fee_idr,
                    'usd' => (float) $d->fee_usd,
                    'cny' => (float) $d->fee_cny,
                    'sar' => (float) $d->fee_sar,
                ],
            ];
        });

        return response()->json([
            'service' => [
                'id' => $service->id,
                'name' => $service->name,
                'slug' => $service->slug,
                'has_sohibul' => $service->has_sohibul,
                'has_cooking_option' => $service->has_cooking_option,
                'default_cooking_option' => $service->default_cooking_option,
                'cooking_fee' => [
                    'idr' => (float) $service->cooking_fee_idr,
                    'usd' => (float) $service->cooking_fee_usd,
                    'cny' => (float) $service->cooking_fee_cny,
                    'sar' => (float) $service->cooking_fee_sar,
                ],
            ],
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'description' => $product->description,
                'prices' => [
                    'idr' => (float) $product->price,
                    'usd' => (float) ($product->price_usd ?? 0),
                    'cny' => (float) ($product->price_cny ?? 0),
                    'sar' => (float) ($product->price_sar ?? 0),
                ],
                'weight_estimate_kg' => (float) $product->weight_estimate_kg,
                'stock' => (int) $product->stock,
                'max_sohibul' => (int) $product->max_sohibul,
                'primary_image_url' => LandingController::formatMediaUrl($product->primary_image_url),
                'gallery' => $gallery,
                'variants' => $variants,
            ],
            'distribution_options' => $distributions,
        ]);
    }
}
