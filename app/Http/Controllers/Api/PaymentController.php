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
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized.',
            ], 403);
        }

        if ($transaction->payment_method !== PaymentMethod::Midtrans || $transaction->payment_status !== PaymentStatus::Pending) {
            return response()->json([
                'status' => 'error',
                'message' => 'Transaksi ini tidak dapat dibayar melalui Midtrans.',
            ], 422);
        }

        try {
            $token = $midtrans->createSnapTransaction($transaction);

            return response()->json([
                'status' => 'success',
                'snap_token' => $token,
                'client_key' => config('midtrans.client_key'),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
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
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized.',
            ], 403);
        }

        if ($transaction->payment_method !== PaymentMethod::ManualTransfer || $transaction->payment_status !== PaymentStatus::Pending) {
            return response()->json([
                'status' => 'error',
                'message' => 'Transaksi ini tidak menerima bukti transfer.',
            ], 422);
        }

        $request->validate([
            'proof' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
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

        $fullUrl = LandingController::formatMediaUrl($url);

        $transaction->update([
            'manual_transfer_proof_url' => $url,
            'payment_status' => PaymentStatus::Paid,
            'status' => \App\Enums\TransactionStatus::Dibayar,
        ]);

        // Kirim Push Notification otomatis ke HP user jika aktif
        if ($transaction->user) {
            try {
                app(\App\Services\Notification\WebPushService::class)->sendToUser(
                    $transaction->user,
                    '✅ Bukti Pembayaran Diterima!',
                    "Pembayaran pesanan #{$transaction->transaction_code} telah diterima. Status pesanan kini: Dibayar.",
                    route('transactions.show', $transaction->transaction_code)
                );
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Gagal push notif manual proof API: ' . $e->getMessage());
            }
        }

        $transaction->refresh();

        return response()->json([
            'status' => 'success',
            'message' => 'Bukti transfer berhasil dikirim. Status transaksi kini telah Dibayar.',
            'data' => [
                'transaction_code' => $transaction->transaction_code,
                'proof_url' => $fullUrl,
                'payment_status' => $transaction->payment_status->value,
                'payment_status_label' => $transaction->payment_status->label(),
                'status' => $transaction->status->value,
                'status_label' => $transaction->status->label(),
            ],
        ]);
    }
}
