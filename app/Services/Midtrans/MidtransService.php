<?php

namespace App\Services\Midtrans;

use App\Enums\PaymentStatus;
use App\Enums\TransactionStatus;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransService
{
    public function __construct()
    {
        $serverKey = config('midtrans.server_key') ?: env('MIDTRANS_SERVER_KEY', 'SB-Mid-server-UWdxhKL11SJqG3c8T0TFyfvo');
        $clientKey = config('midtrans.client_key') ?: env('MIDTRANS_CLIENT_KEY', 'SB-Mid-client-yq36YrCJTtq0Cb2A');
        $isProduction = config('midtrans.is_production', false);

        Config::$serverKey = (string) $serverKey;
        Config::$clientKey = (string) $clientKey;
        Config::$isProduction = (bool) $isProduction;
        Config::$isSanitized = (bool) config('midtrans.is_sanitized', true);
        Config::$is3ds = (bool) config('midtrans.is_3ds', true);
    }

    /**
     * Create (or reuse) a Snap token for the transaction.
     *
     * Idempotent: while payment is still pending, the previously issued
     * token is returned so the user can reopen the Snap popup.
     */
    public function createSnapTransaction(Transaction $transaction): string
    {
        if ($transaction->midtrans_snap_token && $transaction->payment_status === PaymentStatus::Pending) {
            return $transaction->midtrans_snap_token;
        }

        $serverKey = config('midtrans.server_key') ?: env('MIDTRANS_SERVER_KEY', 'SB-Mid-server-UWdxhKL11SJqG3c8T0TFyfvo');
        $clientKey = config('midtrans.client_key') ?: env('MIDTRANS_CLIENT_KEY', 'SB-Mid-client-yq36YrCJTtq0Cb2A');
        $isProduction = config('midtrans.is_production', false);

        Config::$serverKey = (string) $serverKey;
        Config::$clientKey = (string) $clientKey;
        Config::$isProduction = (bool) $isProduction;
        Config::$isSanitized = (bool) config('midtrans.is_sanitized', true);
        Config::$is3ds = (bool) config('midtrans.is_3ds', true);

        $transaction->loadMissing(['user', 'product']);

        $params = [
            'transaction_details' => [
                'order_id' => $transaction->transaction_code,
                'gross_amount' => (int) round($transaction->total_amount),
            ],
            'customer_details' => [
                'first_name' => $transaction->user ? $transaction->user->name : ($transaction->recipient_name ?? 'Pelanggan'),
                'email' => $transaction->user ? $transaction->user->email : 'customer@piramidqurban.com',
                'phone' => $transaction->recipient_phone ?? '',
            ],
            'item_details' => [
                [
                    'id' => (string) ($transaction->product_id ?? 'item-1'),
                    // Midtrans limits item name to 50 chars.
                    'name' => Str::limit($transaction->product ? $transaction->product->name : 'Hewan Qurban/Aqiqah', 50, ''),
                    'quantity' => (int) $transaction->quantity,
                    'price' => (int) round($transaction->unit_price),
                ],
            ],
        ];

        try {
            $token = Snap::getSnapToken($params);
        } catch (\Throwable $e) {
            Log::error('Gagal membuat Midtrans Snap token: ' . $e->getMessage(), [
                'params' => $params,
                'error' => $e->getTraceAsString(),
            ]);
            throw $e;
        }

        $transaction->fill([
            'midtrans_order_id' => $transaction->transaction_code,
            'midtrans_snap_token' => $token,
        ])->save();

        return $token;
    }

    /**
     * Verify the SHA-512 signature of a Midtrans HTTP notification.
     */
    public function verifySignature(array $payload): bool
    {
        $signature = $payload['signature_key'] ?? null;

        if (! is_string($signature) || $signature === '') {
            return false;
        }

        $expected = hash('sha512', sprintf(
            '%s%s%s%s',
            $payload['order_id'] ?? '',
            $payload['status_code'] ?? '',
            $payload['gross_amount'] ?? '',
            config('midtrans.server_key'),
        ));

        return hash_equals($expected, $signature);
    }

    /**
     * Apply a (signature-verified) Midtrans notification to the matching transaction.
     */
    public function handleNotification(array $payload): void
    {
        DB::transaction(function () use ($payload): void {
            $orderId = $payload['order_id'] ?? null;

            $transaction = Transaction::query()
                ->where('midtrans_order_id', $orderId)
                ->orWhere('transaction_code', $orderId)
                ->lockForUpdate()
                ->first();

            if (! $transaction) {
                Log::warning('Midtrans notification for unknown order.', ['order_id' => $orderId]);

                return;
            }

            $wasPending = $transaction->payment_status === PaymentStatus::Pending;

            $transaction->fill([
                'midtrans_transaction_id' => $payload['transaction_id'] ?? $transaction->midtrans_transaction_id,
                'midtrans_payment_type' => $payload['payment_type'] ?? $transaction->midtrans_payment_type,
                'midtrans_transaction_status' => $payload['transaction_status'] ?? $transaction->midtrans_transaction_status,
                'midtrans_fraud_status' => $payload['fraud_status'] ?? $transaction->midtrans_fraud_status,
                'midtrans_raw_response' => $payload,
            ]);

            if (! empty($payload['va_numbers'][0]['va_number'])) {
                $transaction->midtrans_va_number = $payload['va_numbers'][0]['va_number'];
            }

            $transactionStatus = $payload['transaction_status'] ?? null;
            $fraudStatus = $payload['fraud_status'] ?? null;

            $isSettled = $transactionStatus === 'settlement'
                || ($transactionStatus === 'capture' && $fraudStatus === 'accept');

            if ($isSettled) {
                $transaction->payment_status = PaymentStatus::Paid;
                $transaction->midtrans_settlement_time = $payload['settlement_time'] ?? now();

                if ($transaction->status === TransactionStatus::Menunggu) {
                    $transaction->status = TransactionStatus::Dibayar;
                }
            } elseif (in_array($transactionStatus, ['deny', 'cancel'], true)) {
                $transaction->payment_status = PaymentStatus::Cancelled;
            } elseif ($transactionStatus === 'expire') {
                $transaction->payment_status = PaymentStatus::Expired;
            }
            // 'pending' (and unknown statuses) leave payment_status untouched.

            $transaction->save();

            // Give the reserved stock back exactly once, only when the payment
            // was still pending before this notification flipped it to a
            // terminal failure state.
            $isFailure = in_array($transactionStatus, ['deny', 'cancel', 'expire'], true);

            if ($isFailure && $wasPending && $transaction->product) {
                $transaction->product->increment('stock', $transaction->quantity);
            }
        });
    }
}
