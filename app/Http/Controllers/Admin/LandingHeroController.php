<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LandingHero;
use Cloudinary\Cloudinary;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class LandingHeroController extends Controller
{
    /**
     * Tampilkan daftar slide landing hero / onboarding.
     */
    public function index(): Response
    {
        $heroes = LandingHero::query()
            ->orderBy('order')
            ->get();

        return Inertia::render('Admin/Heroes/Index', [
            'heroes' => $heroes,
        ]);
    }

    /**
     * Form tambah slide landing hero.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Heroes/Form', [
            'hero' => null,
        ]);
    }

    /**
     * Simpan slide hero baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'image_file' => ['nullable', 'image', 'max:5120'], // Max 5MB
            'image_url' => ['nullable', 'string', 'max:1000'],
            'icon_name' => ['nullable', 'string', 'max:100'],
            'order' => ['required', 'integer'],
            'is_active' => ['required', 'boolean'],
        ]);

        $imageUrl = $validated['image_url'] ?? '';

        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $cloudName = config('cloudinary.cloud_name') ?? env('CLOUDINARY_CLOUD_NAME');

            if ($cloudName) {
                try {
                    $cloudinary = new Cloudinary();
                    $uploaded = $cloudinary->uploadApi()->upload($file->getRealPath(), [
                        'folder' => 'qurban-pyramid/heroes',
                        'resource_type' => 'image',
                    ]);
                    $imageUrl = $uploaded['secure_url'];
                } catch (\Throwable) {
                    $filename = Str::random(24).'.'.$file->getClientOriginalExtension();
                    $file->storeAs('heroes', $filename, 'public');
                    $imageUrl = '/storage/heroes/'.$filename;
                }
            } else {
                $filename = Str::random(24).'.'.$file->getClientOriginalExtension();
                $file->storeAs('heroes', $filename, 'public');
                $imageUrl = '/storage/heroes/'.$filename;
            }
        }

        if (empty($imageUrl)) {
            return back()->withErrors(['image_file' => 'Wajib upload gambar atau isi URL gambar.']);
        }

        LandingHero::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'image_url' => $imageUrl,
            'icon_name' => $validated['icon_name'] ?? 'verified_user_rounded',
            'order' => $validated['order'],
            'is_active' => $validated['is_active'],
        ]);

        return redirect()->route('admin.heroes.index')->with('success', 'Slide Hero berhasil ditambahkan.');
    }

    /**
     * Form edit slide hero.
     */
    public function edit(LandingHero $hero): Response
    {
        return Inertia::render('Admin/Heroes/Form', [
            'hero' => $hero,
        ]);
    }

    /**
     * Update slide hero.
     */
    public function update(Request $request, LandingHero $hero): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'image_file' => ['nullable', 'image', 'max:5120'],
            'image_url' => ['nullable', 'string', 'max:1000'],
            'icon_name' => ['nullable', 'string', 'max:100'],
            'order' => ['required', 'integer'],
            'is_active' => ['required', 'boolean'],
        ]);

        $imageUrl = $validated['image_url'] ?? $hero->image_url;

        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $cloudName = config('cloudinary.cloud_name') ?? env('CLOUDINARY_CLOUD_NAME');

            if ($cloudName) {
                try {
                    $cloudinary = new Cloudinary();
                    $uploaded = $cloudinary->uploadApi()->upload($file->getRealPath(), [
                        'folder' => 'qurban-pyramid/heroes',
                        'resource_type' => 'image',
                    ]);
                    $imageUrl = $uploaded['secure_url'];
                } catch (\Throwable) {
                    $filename = Str::random(24).'.'.$file->getClientOriginalExtension();
                    $file->storeAs('heroes', $filename, 'public');
                    $imageUrl = '/storage/heroes/'.$filename;
                }
            } else {
                $filename = Str::random(24).'.'.$file->getClientOriginalExtension();
                $file->storeAs('heroes', $filename, 'public');
                $imageUrl = '/storage/heroes/'.$filename;
            }
        }

        $hero->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'image_url' => $imageUrl,
            'icon_name' => $validated['icon_name'] ?? 'verified_user_rounded',
            'order' => $validated['order'],
            'is_active' => $validated['is_active'],
        ]);

        return redirect()->route('admin.heroes.index')->with('success', 'Slide Hero berhasil diperbarui.');
    }

    /**
     * Toggle status aktif/non-aktif slide hero.
     */
    public function toggleStatus(LandingHero $hero): RedirectResponse
    {
        $hero->update([
            'is_active' => ! $hero->is_active,
        ]);

        return back()->with('success', 'Status slide berhasil diubah.');
    }

    /**
     * Hapus slide hero.
     */
    public function destroy(LandingHero $hero): RedirectResponse
    {
        $hero->delete();

        return redirect()->route('admin.heroes.index')->with('success', 'Slide Hero berhasil dihapus.');
    }
}
