<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Service;
use Inertia\Inertia;
use Inertia\Response;

class CatalogController extends Controller
{
    /**
     * USR-02 — Daftar produk aktif milik sebuah layanan (publik).
     */
    public function index(Service $service): Response
    {
        abort_unless($service->is_active, 404);

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
                'products.primary_image_url',
            ]);

        return Inertia::render('Catalog/Index', [
            'service' => $service->only(['id', 'name', 'slug', 'description', 'cover_image_url']),
            'products' => $products,
        ]);
    }

    /**
     * USR-02 — Detail produk dalam konteks layanan (publik).
     * Binding {product:slug} otomatis di-scope ke relasi products milik service.
     */
    public function show(Service $service, Product $product): Response
    {
        abort_unless($service->is_active && $product->is_active, 404);

        return Inertia::render('Catalog/Show', [
            'service' => $service->only(['id', 'name', 'slug']),
            'product' => $product->only([
                'id',
                'name',
                'slug',
                'description',
                'price',
                'weight_estimate_kg',
                'stock',
                'primary_image_url',
                'gallery',
            ]),
        ]);
    }
}
