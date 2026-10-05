<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DistributionOption;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DistributionOptionController extends Controller
{
    public function index(): Response
    {
        $distributions = DistributionOption::query()
            ->orderBy('order')
            ->get();

        return Inertia::render('Admin/Distributions/Index', [
            'distributions' => $distributions,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Distributions/Form', [
            'distribution' => null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name_id' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'name_zh' => ['nullable', 'string', 'max:255'],
            'name_ar' => ['nullable', 'string', 'max:255'],
            'description_id' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'description_zh' => ['nullable', 'string'],
            'description_ar' => ['nullable', 'string'],
            'fee_idr' => ['required', 'numeric', 'min:0'],
            'fee_usd' => ['nullable', 'numeric', 'min:0'],
            'fee_cny' => ['nullable', 'numeric', 'min:0'],
            'fee_sar' => ['nullable', 'numeric', 'min:0'],
            'order' => ['required', 'integer', 'min:0'],
            'is_active' => ['required', 'boolean'],
        ]);

        DistributionOption::create($validated);

        return redirect()->route('admin.distribusi.index')->with('success', 'Opsi penyaluran / distribusi berhasil dibuat.');
    }

    public function edit(DistributionOption $distribusi): Response
    {
        return Inertia::render('Admin/Distributions/Form', [
            'distribution' => $distribusi,
        ]);
    }

    public function update(Request $request, DistributionOption $distribusi): RedirectResponse
    {
        $validated = $request->validate([
            'name_id' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'name_zh' => ['nullable', 'string', 'max:255'],
            'name_ar' => ['nullable', 'string', 'max:255'],
            'description_id' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'description_zh' => ['nullable', 'string'],
            'description_ar' => ['nullable', 'string'],
            'fee_idr' => ['required', 'numeric', 'min:0'],
            'fee_usd' => ['nullable', 'numeric', 'min:0'],
            'fee_cny' => ['nullable', 'numeric', 'min:0'],
            'fee_sar' => ['nullable', 'numeric', 'min:0'],
            'order' => ['required', 'integer', 'min:0'],
            'is_active' => ['required', 'boolean'],
        ]);

        $distribusi->update($validated);

        return redirect()->route('admin.distribusi.index')->with('success', 'Opsi penyaluran berhasil diperbarui.');
    }

    public function toggleStatus(DistributionOption $distribusi): RedirectResponse
    {
        $distribusi->update([
            'is_active' => ! $distribusi->is_active,
        ]);

        return back()->with('success', 'Status opsi distribusi berhasil diubah.');
    }

    public function destroy(DistributionOption $distribusi): RedirectResponse
    {
        $distribusi->delete();

        return redirect()->route('admin.distribusi.index')->with('success', 'Opsi distribusi berhasil dihapus.');
    }
}
