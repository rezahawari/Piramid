<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Product;
use App\Models\Service;
use App\Services\Cloudinary\CloudinaryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Products/Index', [
            'products' => Product::with('services:id,name')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Products/Form', [
            'product' => null,
            'services' => Service::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(ProductRequest $request, CloudinaryService $cloudinary): RedirectResponse
    {
        $data = Arr::except($request->validated(), ['service_ids', 'image_file']);

        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            if ($cloudinary->isConfigured()) {
                $upload = $cloudinary->uploadFile(
                    $file,
                    config('cloudinary.upload_folder') . '/products',
                );
                $data['primary_image_url'] = $upload['secure_url'];
            } else {
                $path = $file->store('products', 'public');
                $data['primary_image_url'] = '/storage/' . $path;
            }
        }

        $product = Product::create($data);

        $product->services()->sync($request->validated('service_ids') ?? []);

        return redirect()
            ->route('admin.produk.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $produk): Response
    {
        return Inertia::render('Admin/Products/Form', [
            'product' => $produk->load('services:id'),
            'services' => Service::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(ProductRequest $request, Product $produk, CloudinaryService $cloudinary): RedirectResponse
    {
        $data = Arr::except($request->validated(), ['service_ids', 'image_file']);

        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            if ($cloudinary->isConfigured()) {
                $upload = $cloudinary->uploadFile(
                    $file,
                    config('cloudinary.upload_folder') . '/products',
                );
                $data['primary_image_url'] = $upload['secure_url'];
            } else {
                $path = $file->store('products', 'public');
                $data['primary_image_url'] = '/storage/' . $path;
            }
        }

        $produk->update($data);

        $produk->services()->sync($request->validated('service_ids') ?? []);

        return redirect()
            ->route('admin.produk.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $produk): RedirectResponse
    {
        $produk->delete();

        return redirect()
            ->route('admin.produk.index')
            ->with('success', 'Produk berhasil dihapus.');
    }
}
