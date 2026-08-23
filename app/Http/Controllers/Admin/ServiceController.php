<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ServiceRequest;
use App\Models\Service;
use App\Services\Cloudinary\CloudinaryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ServiceController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Services/Index', [
            'services' => Service::withCount('products')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Services/Form', [
            'service' => null,
        ]);
    }

    public function store(ServiceRequest $request, CloudinaryService $cloudinary): RedirectResponse
    {
        $data = Arr::except($request->validated(), ['image_file']);

        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            if ($cloudinary->isConfigured()) {
                $upload = $cloudinary->uploadFile(
                    $file,
                    config('cloudinary.upload_folder') . '/services',
                );
                $data['cover_image_url'] = $upload['secure_url'];
            } else {
                $path = $file->store('services', 'public');
                $data['cover_image_url'] = '/storage/' . $path;
            }
        }

        Service::create($data);

        return redirect()
            ->route('admin.layanan.index')
            ->with('success', 'Layanan berhasil ditambahkan.');
    }

    public function edit(Service $layanan): Response
    {
        return Inertia::render('Admin/Services/Form', [
            'service' => $layanan,
        ]);
    }

    public function update(ServiceRequest $request, Service $layanan, CloudinaryService $cloudinary): RedirectResponse
    {
        $data = Arr::except($request->validated(), ['image_file']);

        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            if ($cloudinary->isConfigured()) {
                $upload = $cloudinary->uploadFile(
                    $file,
                    config('cloudinary.upload_folder') . '/services',
                );
                $data['cover_image_url'] = $upload['secure_url'];
            } else {
                $path = $file->store('services', 'public');
                $data['cover_image_url'] = '/storage/' . $path;
            }
        }

        $layanan->update($data);

        return redirect()
            ->route('admin.layanan.index')
            ->with('success', 'Layanan berhasil diperbarui.');
    }

    public function destroy(Service $layanan): RedirectResponse
    {
        $layanan->delete();

        return redirect()
            ->route('admin.layanan.index')
            ->with('success', 'Layanan berhasil dihapus.');
    }
}
