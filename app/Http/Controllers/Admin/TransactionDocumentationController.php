<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TransactionStatus;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TransactionDocumentation;
use App\Services\Cloudinary\CloudinaryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class TransactionDocumentationController extends Controller
{
    /**
     * ADM-04 — Simpan dokumentasi. Dua mode:
     * (a) hasil direct-browser upload: file_url + cloudinary_public_id + type
     * (b) server-proxy: file mentah diunggah lewat CloudinaryService
     */
    public function store(
        Request $request,
        Transaction $transaction,
        CloudinaryService $cloudinary,
    ): RedirectResponse {
        $validated = $request->validate([
            'stage' => ['required', Rule::enum(TransactionStatus::class)],
            'caption' => ['nullable', 'string', 'max:255'],
            'file_url' => ['required_without:file', 'nullable', 'url'],
            'cloudinary_public_id' => ['nullable', 'string'],
            'type' => ['required_with:file_url', 'nullable', Rule::in(['photo', 'video'])],
            'file' => ['required_without:file_url', 'nullable', 'file', 'mimetypes:image/*,video/*', 'max:20480'],
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');

            if ($cloudinary->isConfigured()) {
                try {
                    $upload = $cloudinary->uploadFile(
                        $file,
                        config('cloudinary.upload_folder').'/'.$transaction->transaction_code,
                    );

                    $fileUrl = $upload['secure_url'];
                    $publicId = $upload['public_id'];
                    $type = $upload['resource_type'] === 'video' ? 'video' : 'photo';
                } catch (\Throwable $e) {
                    Log::warning('Cloudinary upload gagal, fallback ke local storage: '.$e->getMessage());
                    $filename = \Illuminate\Support\Str::random(24).'.'.$file->getClientOriginalExtension();
                    $file->storeAs('dokumentasi', $filename, 'public');
                    $fileUrl = '/storage/dokumentasi/'.$filename;
                    $publicId = null;
                    $type = str_starts_with($file->getMimeType(), 'video/') ? 'video' : 'photo';
                }
            } else {
                // Fallback dev lokal / container tanpa kredensial Cloudinary
                $filename = \Illuminate\Support\Str::random(24).'.'.$file->getClientOriginalExtension();
                $file->storeAs('dokumentasi', $filename, 'public');
                $fileUrl = '/storage/dokumentasi/'.$filename;
                $publicId = null;
                $type = str_starts_with($file->getMimeType(), 'video/') ? 'video' : 'photo';
            }
        } else {
            $fileUrl = $validated['file_url'];
            $publicId = $validated['cloudinary_public_id'] ?? null;
            $type = $validated['type'];
        }

        $doc = $transaction->documentations()->create([
            'stage' => $validated['stage'],
            'caption' => $validated['caption'] ?? null,
            'file_url' => $fileUrl,
            'cloudinary_public_id' => $publicId,
            'type' => $type,
            'uploaded_by' => $request->user()->id,
        ]);

        // Kirim Push Notification otomatis ke smartphone user pemesan
        if ($transaction->user) {
            try {
                $stageName = match($validated['stage']->value ?? $validated['stage']) {
                    'hewan_disiapkan' => 'Hewan Disiapkan',
                    'tersembelih' => 'Penyembelihan Hewan',
                    'didistribusikan' => 'Pendistribusian Daging',
                    default => 'Pembaruan Tahapan',
                };

                app(\App\Services\Notification\WebPushService::class)->sendToUser(
                    $transaction->user,
                    '📹 Dokumentasi Baru Tersedia!',
                    "Admin telah mengunggah dokumentasi tahap {$stageName} untuk pesanan #{$transaction->transaction_code}.",
                    route('transactions.show', $transaction->transaction_code)
                );
            } catch (\Throwable $e) {
                Log::warning('Gagal memicu push notifikasi dokumentasi: ' . $e->getMessage());
            }
        }

        return redirect()
            ->back()
            ->with('success', 'Dokumentasi berhasil diunggah.');
    }

    public function destroy(TransactionDocumentation $documentation, CloudinaryService $cloudinary): RedirectResponse
    {
        if ($documentation->cloudinary_public_id && $cloudinary->isConfigured()) {
            try {
                $cloudinary->delete(
                    $documentation->cloudinary_public_id,
                    $documentation->type === 'video' ? 'video' : 'image',
                );
            } catch (\Throwable $e) {
                Log::warning('Gagal menghapus media Cloudinary.', [
                    'public_id' => $documentation->cloudinary_public_id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $documentation->delete();

        return back()->with('success', 'Dokumentasi dihapus.');
    }
}
