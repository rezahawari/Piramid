<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentationGallery;
use Cloudinary\Cloudinary;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class DocumentationGalleryController extends Controller
{
    /**
     * Display a listing of documentation items.
     */
    public function index(): Response
    {
        $items = DocumentationGallery::query()
            ->orderBy('order_index')
            ->latest()
            ->get();

        return Inertia::render('Admin/Documentation/Index', [
            'items' => $items,
        ]);
    }

    /**
     * Show the form for creating a new documentation item.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Documentation/Form', [
            'item' => null,
        ]);
    }

    /**
     * Store a newly created documentation item in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:image,video,youtube'],
            'media_file' => ['nullable', 'file', 'max:25600'], // max 25MB
            'file_url' => ['nullable', 'string', 'max:1000'],
            'youtube_url' => ['nullable', 'string', 'max:1000'],
            'description' => ['nullable', 'string'],
            'category' => ['required', 'string', 'max:100'],
            'order_index' => ['nullable', 'integer'],
            'is_active' => ['required', 'boolean'],
        ]);

        $fileUrl = $validated['file_url'] ?? '';

        if ($request->hasFile('media_file')) {
            $file = $request->file('media_file');
            $cloudName = config('cloudinary.cloud_name') ?? env('CLOUDINARY_CLOUD_NAME');

            if ($cloudName) {
                try {
                    $cloudinary = new Cloudinary();
                    $uploaded = $cloudinary->uploadApi()->upload($file->getRealPath(), [
                        'folder' => 'qurban-pyramid/galeri',
                        'resource_type' => 'auto',
                    ]);
                    $fileUrl = $uploaded['secure_url'];
                } catch (\Throwable) {
                    $filename = Str::random(24).'.'.$file->getClientOriginalExtension();
                    $file->storeAs('documentation', $filename, 'public');
                    $fileUrl = '/storage/documentation/'.$filename;
                }
            } else {
                $filename = Str::random(24).'.'.$file->getClientOriginalExtension();
                $file->storeAs('documentation', $filename, 'public');
                $fileUrl = '/storage/documentation/'.$filename;
            }
        }

        DocumentationGallery::create([
            'title' => $validated['title'],
            'type' => $validated['type'],
            'file_url' => $fileUrl,
            'youtube_url' => $validated['youtube_url'] ?? null,
            'description' => $validated['description'] ?? null,
            'category' => $validated['category'],
            'order_index' => $validated['order_index'] ?? 0,
            'is_active' => $validated['is_active'],
        ]);

        return redirect()
            ->route('admin.galeri.index')
            ->with('success', "Item dokumentasi '{$validated['title']}' berhasil ditambahkan.");
    }

    /**
     * Show the form for editing the specified documentation item.
     */
    public function edit(DocumentationGallery $galeri): Response
    {
        return Inertia::render('Admin/Documentation/Form', [
            'item' => $galeri,
        ]);
    }

    /**
     * Update the specified documentation item in storage.
     */
    public function update(Request $request, DocumentationGallery $galeri): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:image,video,youtube'],
            'media_file' => ['nullable', 'file', 'max:25600'],
            'file_url' => ['nullable', 'string', 'max:1000'],
            'youtube_url' => ['nullable', 'string', 'max:1000'],
            'description' => ['nullable', 'string'],
            'category' => ['required', 'string', 'max:100'],
            'order_index' => ['nullable', 'integer'],
            'is_active' => ['required', 'boolean'],
        ]);

        $fileUrl = $validated['file_url'] ?? $galeri->file_url;

        if ($request->hasFile('media_file')) {
            $file = $request->file('media_file');
            $cloudName = config('cloudinary.cloud_name') ?? env('CLOUDINARY_CLOUD_NAME');

            if ($cloudName) {
                try {
                    $cloudinary = new Cloudinary();
                    $uploaded = $cloudinary->uploadApi()->upload($file->getRealPath(), [
                        'folder' => 'qurban-pyramid/galeri',
                        'resource_type' => 'auto',
                    ]);
                    $fileUrl = $uploaded['secure_url'];
                } catch (\Throwable) {
                    $filename = Str::random(24).'.'.$file->getClientOriginalExtension();
                    $file->storeAs('documentation', $filename, 'public');
                    $fileUrl = '/storage/documentation/'.$filename;
                }
            } else {
                $filename = Str::random(24).'.'.$file->getClientOriginalExtension();
                $file->storeAs('documentation', $filename, 'public');
                $fileUrl = '/storage/documentation/'.$filename;
            }
        }

        $galeri->update([
            'title' => $validated['title'],
            'type' => $validated['type'],
            'file_url' => $fileUrl,
            'youtube_url' => $validated['youtube_url'] ?? null,
            'description' => $validated['description'] ?? null,
            'category' => $validated['category'],
            'order_index' => $validated['order_index'] ?? 0,
            'is_active' => $validated['is_active'],
        ]);

        return redirect()
            ->route('admin.galeri.index')
            ->with('success', "Item dokumentasi '{$galeri->title}' berhasil diperbarui.");
    }

    /**
     * Remove the specified documentation item from storage.
     */
    public function destroy(DocumentationGallery $galeri): RedirectResponse
    {
        $title = $galeri->title;
        $galeri->delete();

        return redirect()
            ->route('admin.galeri.index')
            ->with('success', "Item dokumentasi '{$title}' berhasil dihapus.");
    }
}
