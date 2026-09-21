<?php

namespace App\Http\Controllers\Api;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Services\Cloudinary\CloudinaryService;
use App\Services\Midtrans\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    /**
     * Issue (or reuse) Midtrans Snap token untuk transaksi pending.
     */
    public function snapToken(Request $request, Transaction $transaction, MidtransService $midtrans): JsonResponse
    {
        if ($request->user()->id !== $transaction->user_id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        if ($transaction->payment_method !== PaymentMethod::Midtrans || $transaction->payment_status !== PaymentStatus::Pending) {
            return response()->json([
                'message' => 'Transaksi ini tidak dapat dibayar melalui Midtrans.',
            ], 422);
        }

        try {
            $token = $midtrans->createSnapTransaction($transaction);

            return response()->json([
                'snap_token' => $token,
                'client_key' => config('midtrans.client_key'),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Gagal menghubungkan ke payment gateway: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Upload bukti transfer manual.
     */
    public function uploadManualProof(
        Request $request,
        Transaction $transaction,
        CloudinaryService $cloudinary,
    ): JsonResponse {
        if ($request->user()->id !== $transaction->user_id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        if ($transaction->payment_method !== PaymentMethod::ManualTransfer || $transaction->payment_status !== PaymentStatus::Pending) {
            return response()->json([
                'message' => 'Transaksi ini tidak menerima bukti transfer.',
            ], 422);
        }

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
                $filename = Str::random(24).'.'.$request->file('proof')->getClientOriginalExtension();
                $request->file('proof')->storeAs('bukti-transfer', $filename, 'public');
                $url = '/storage/bukti-transfer/'.$filename;
            }
        } else {
            $filename = Str::random(24).'.'.$request->file('proof')->getClientOriginalExtension();
            $request->file('proof')->storeAs('bukti-transfer', $filename, 'public');
            $url = '/storage/bukti-transfer/'.$filename;
        }

        $transaction->update(['manual_transfer_proof_url' => $url]);

        return response()->json([
            'message' => 'Bukti transfer terkirim, menunggu verifikasi admin.',
            'proof_url' => $url,
        ]);
    }
}
