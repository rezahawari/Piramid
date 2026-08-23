<?php

namespace App\Http\Controllers;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Transaction;
use App\Services\Cloudinary\CloudinaryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ManualTransferController extends Controller
{
    /**
     * USR-04 — Unggah bukti transfer manual milik user.
     */
    public function store(
        Request $request,
        Transaction $transaction,
        CloudinaryService $cloudinary,
    ): RedirectResponse {
        abort_unless($request->user()->id === $transaction->user_id, 403);

        abort_unless(
            $transaction->payment_method === PaymentMethod::ManualTransfer
                && $transaction->payment_status === PaymentStatus::Pending,
            422,
            'Transaksi ini tidak menerima bukti transfer.',
        );

        $request->validate([
            'proof' => ['required', 'image', 'max:5120'],
        ]);

        if ($cloudinary->isConfigured()) {
            try {
                $upload = $cloudinary->uploadFile(
                    $request->file('proof'),
                    config('cloudinary.upload_folder').'/bukti-transfer',
                );
                $url = $upload['secure_url'];
            } catch (\Throwable) {
                $filename = \Illuminate\Support\Str::random(24).'.'.$request->file('proof')->getClientOriginalExtension();
                $request->file('proof')->storeAs('bukti-transfer', $filename, 'public');
                $url = '/storage/bukti-transfer/'.$filename;
            }
        } else {
            // Fallback dev lokal / tanpa kredensial Cloudinary.
            $filename = \Illuminate\Support\Str::random(24).'.'.$request->file('proof')->getClientOriginalExtension();
            $request->file('proof')->storeAs('bukti-transfer', $filename, 'public');
            $url = '/storage/bukti-transfer/'.$filename;
        }

        $transaction->update(['manual_transfer_proof_url' => $url]);

        return back()->with('success', 'Bukti transfer terkirim, menunggu verifikasi admin.');
    }
}
