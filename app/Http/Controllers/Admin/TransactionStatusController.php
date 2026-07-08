<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PaymentStatus;
use App\Enums\TransactionStatus;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Services\Cloudinary\CloudinaryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class TransactionStatusController extends Controller
{
    /**
     * ADM-04 — Detail transaksi: stepper status + manajemen dokumentasi.
     */
    public function show(Transaction $transaction, CloudinaryService $cloudinary): Response
    {
        $transaction->load([
            'user:id,name,email',
            'service:id,name',
            'product:id,name,primary_image_url',
            'approvedBy:id,name',
            'documentations' => fn ($q) => $q->with('uploadedBy:id,name')->orderBy('created_at'),
        ]);

        $uploadFolder = config('cloudinary.upload_folder').'/'.$transaction->transaction_code;

        return Inertia::render('Admin/Transactions/Show', [
            'transaction' => $transaction,
            'stages' => collect(TransactionStatus::pipeline())
                ->map(fn (TransactionStatus $s) => ['value' => $s->value, 'label' => $s->label()])
                ->values(),
            'cloudinary_configured' => $cloudinary->isConfigured(),
            'upload_signature' => $cloudinary->isConfigured()
                ? $cloudinary->generateUploadSignature($uploadFolder)
                : null,
        ]);
    }

    /**
     * ADM-04 — Naikkan tahap status. Tidak boleh mundur, tidak boleh
     * melewati pembayaran, dan tiap tahap wajib punya bukti dokumentasi.
     */
    public function update(Request $request, Transaction $transaction): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::enum(TransactionStatus::class)],
        ]);

        $new = TransactionStatus::from($validated['status']);
        $current = $transaction->status;

        if ($new->order() <= $current->order()) {
            throw ValidationException::withMessages([
                'status' => 'Status tidak boleh mundur atau tetap.',
            ]);
        }

        if ($new->order() > $current->order() + 1) {
            throw ValidationException::withMessages([
                'status' => 'Status hanya boleh naik satu tahap.',
            ]);
        }

        if ($new->order() >= TransactionStatus::Dibayar->order()
            && $transaction->payment_status !== PaymentStatus::Paid) {
            throw ValidationException::withMessages([
                'status' => 'Pembayaran belum lunas — status tidak dapat dinaikkan.',
            ]);
        }

        // PRD ADM-04 (revisi): tahap Dibayar tidak butuh bukti dokumentasi — admin
        // sudah verifikasi bukti transfer saat approve(). Dokumentasi baru wajib
        // mulai tahap Hewan Disiapkan sampai Didistribusikan.
        $requiresProof = $current->order() >= TransactionStatus::HewanDisiapkan->order();

        if ($requiresProof && ! $transaction->documentations()->where('stage', $current->value)->exists()) {
            throw ValidationException::withMessages([
                'status' => "Unggah minimal satu dokumentasi untuk tahap \"{$current->label()}\" sebelum melanjutkan.",
            ]);
        }

        $transaction->update(['status' => $new]);

        return back()->with('success', "Status dinaikkan ke {$new->label()}.");
    }
}
