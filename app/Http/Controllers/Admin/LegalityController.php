<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Legality;
use Cloudinary\Cloudinary;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class LegalityController extends Controller
{
    /**
     * Tampilkan daftar dokumen legalitas.
     */
    public function index(): Response
    {
        $legalities = Legality::query()
            ->orderBy('order')
            ->latest()
            ->get();

        return Inertia::render('Admin/Legalities/Index', [
            'legalities' => $legalities,
        ]);
    }

    /**
     * Form tambah dokumen legalitas.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Legalities/Form', [
            'legality' => null,
        ]);
    }

    /**
     * Simpan dokumen legalitas baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'document_number' => ['nullable', 'string', 'max:255'],
            'issuer' => ['nullable', 'string', 'max:255'],
            'issued_date' => ['nullable', 'date'],
            'document_file' => ['nullable', 'file', 'mimes:pdf,png,jpg,jpeg', 'max:15360'], // Max 15MB
            'file_path' => ['nullable', 'string', 'max:1000'],
            'description' => ['nullable', 'string'],
            'order' => ['nullable', 'integer'],
            'is_active' => ['required', 'boolean'],
        ]);

        $filePath = $validated['file_path'] ?? '';
        $fileType = 'pdf';
        $fileSize = null;

        if ($request->hasFile('document_file')) {
            $file = $request->file('document_file');
            $ext = strtolower($file->getClientOriginalExtension());
            $fileType = in_array($ext, ['png', 'jpg', 'jpeg', 'pdf']) ? $ext : 'pdf';
            $sizeInBytes = $file->getSize();
            $fileSize = $sizeInBytes >= 1048576 
                ? number_format($sizeInBytes / 1048576, 2) . ' MB' 
                : number_format($sizeInBytes / 1024, 1) . ' KB';

            $cloudName = config('cloudinary.cloud_name') ?? env('CLOUDINARY_CLOUD_NAME');

            if ($cloudName) {
                try {
                    $cloudinary = new Cloudinary();
                    $uploaded = $cloudinary->uploadApi()->upload($file->getRealPath(), [
                        'folder' => 'qurban-pyramid/legalitas',
                        'resource_type' => 'auto',
                    ]);
                    $filePath = $uploaded['secure_url'];
                } catch (\Throwable) {
                    $filename = Str::random(24) . '.' . $ext;
                    $file->storeAs('legalities', $filename, 'public');
                    $filePath = '/storage/legalities/' . $filename;
                }
            } else {
                $filename = Str::random(24) . '.' . $ext;
                $file->storeAs('legalities', $filename, 'public');
                $filePath = '/storage/legalities/' . $filename;
            }
        }

        if (empty($filePath)) {
            return back()->withErrors(['document_file' => 'File dokumen wajib diunggah (PDF, PNG, JPG).']);
        }

        Legality::create([
            'title' => $validated['title'],
            'document_number' => $validated['document_number'] ?? null,
            'issuer' => $validated['issuer'] ?? null,
            'issued_date' => $validated['issued_date'] ?? null,
            'file_path' => $filePath,
            'file_type' => $fileType,
            'file_size' => $fileSize,
            'description' => $validated['description'] ?? null,
            'order' => $validated['order'] ?? 1,
            'is_active' => $validated['is_active'],
        ]);

        return redirect()
            ->route('admin.legalities.index')
            ->with('success', "Dokumen legalitas '{$validated['title']}' berhasil ditambahkan.");
    }

    /**
     * Form edit dokumen legalitas.
     */
    public function edit(Legality $legality): Response
    {
        return Inertia::render('Admin/Legalities/Form', [
            'legality' => $legality,
        ]);
    }

    /**
     * Perbarui dokumen legalitas.
     */
    public function update(Request $request, Legality $legality): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'document_number' => ['nullable', 'string', 'max:255'],
            'issuer' => ['nullable', 'string', 'max:255'],
            'issued_date' => ['nullable', 'date'],
            'document_file' => ['nullable', 'file', 'mimes:pdf,png,jpg,jpeg', 'max:15360'],
            'file_path' => ['nullable', 'string', 'max:1000'],
            'description' => ['nullable', 'string'],
            'order' => ['nullable', 'integer'],
            'is_active' => ['required', 'boolean'],
        ]);

        $filePath = $legality->file_path;
        $fileType = $legality->file_type;
        $fileSize = $legality->file_size;

        if ($request->hasFile('document_file')) {
            $file = $request->file('document_file');
            $ext = strtolower($file->getClientOriginalExtension());
            $fileType = in_array($ext, ['png', 'jpg', 'jpeg', 'pdf']) ? $ext : 'pdf';
            $sizeInBytes = $file->getSize();
            $fileSize = $sizeInBytes >= 1048576 
                ? number_format($sizeInBytes / 1048576, 2) . ' MB' 
                : number_format($sizeInBytes / 1024, 1) . ' KB';

            $cloudName = config('cloudinary.cloud_name') ?? env('CLOUDINARY_CLOUD_NAME');

            if ($cloudName) {
                try {
                    $cloudinary = new Cloudinary();
                    $uploaded = $cloudinary->uploadApi()->upload($file->getRealPath(), [
                        'folder' => 'qurban-pyramid/legalitas',
                        'resource_type' => 'auto',
                    ]);
                    $filePath = $uploaded['secure_url'];
                } catch (\Throwable) {
                    $filename = Str::random(24) . '.' . $ext;
                    $file->storeAs('legalities', $filename, 'public');
                    $filePath = '/storage/legalities/' . $filename;
                }
            } else {
                $filename = Str::random(24) . '.' . $ext;
                $file->storeAs('legalities', $filename, 'public');
                $filePath = '/storage/legalities/' . $filename;
            }
        } elseif (!empty($validated['file_path'])) {
            $filePath = $validated['file_path'];
        }

        $legality->update([
            'title' => $validated['title'],
            'document_number' => $validated['document_number'] ?? null,
            'issuer' => $validated['issuer'] ?? null,
            'issued_date' => $validated['issued_date'] ?? null,
            'file_path' => $filePath,
            'file_type' => $fileType,
            'file_size' => $fileSize,
            'description' => $validated['description'] ?? null,
            'order' => $validated['order'] ?? $legality->order,
            'is_active' => $validated['is_active'],
        ]);

        return redirect()
            ->route('admin.legalities.index')
            ->with('success', "Dokumen legalitas '{$legality->title}' berhasil diperbarui.");
    }

    /**
     * Hapus dokumen legalitas.
     */
    public function destroy(Legality $legality): RedirectResponse
    {
        $title = $legality->title;
        $legality->delete();

        return redirect()
            ->route('admin.legalities.index')
            ->with('success', "Dokumen '{$title}' berhasil dihapus.");
    }

    /**
     * Toggle status aktif/nonaktif.
     */
    public function toggleStatus(Legality $legality): RedirectResponse
    {
        $legality->update([
            'is_active' => !$legality->is_active,
        ]);

        $statusText = $legality->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Dokumen '{$legality->title}' berhasil {$statusText}.");
    }
}
