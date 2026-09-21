<?php

namespace App\Http\Controllers\Api;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\TransactionStatus;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Services\Midtrans\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * USR-05 — Daftar riwayat transaksi milik user login.
     */
    public function index(Request $request, MidtransService $midtrans): JsonResponse
    {
        // Sync jika ada query param order_id (callback/redirect Midtrans)
        if ($request->filled('order_id')) {
            $order = Transaction::where('transaction_code', $request->order_id)
                ->where('user_id', $request->user()->id)
                ->first();

            if ($order && $order->payment_status === PaymentStatus::Pending) {
                $midtrans->checkTransactionStatus($order);
            }
        }

        $transactions = Transaction::query()
            ->where('user_id', $request->user()->id)
            ->with(['service:id,name,slug,cover_image_url', 'product:id,name,slug,primary_image_url,weight_estimate_kg'])
            ->latest()
            ->paginate(10);

        // Format data agar friendly untuk mobile UI
        $transformed = $transactions->through(function (Transaction $t) {
            return [
                'id' => $t->id,
                'transaction_code' => $t->transaction_code,
                'quantity' => $t->quantity,
                'unit_price' => $t->unit_price,
                'total_amount' => $t->total_amount,
                'status' => $t->status->value,
                'status_label' => $t->status->label(),
                'status_order' => $t->status->order(),
                'payment_status' => $t->payment_status->value,
                'payment_status_label' => $t->payment_status->label(),
                'payment_method' => $t->payment_method->value,
                'payment_method_label' => $t->payment_method->label(),
                'distribution_type' => $t->distribution_type->value,
                'distribution_type_label' => $t->distribution_type->label(),
                'created_at' => $t->created_at,
                'service' => $t->service ? [
                    'id' => $t->service->id,
                    'name' => $t->service->name,
                    'slug' => $t->service->slug,
                    'cover_image_url' => LandingController::formatMediaUrl($t->service->cover_image_url),
                ] : null,
                'product' => $t->product ? [
                    'id' => $t->product->id,
                    'name' => $t->product->name,
                    'slug' => $t->product->slug,
                    'primary_image_url' => LandingController::formatMediaUrl($t->product->primary_image_url),
                    'weight_estimate_kg' => $t->product->weight_estimate_kg,
                ] : null,
            ];
        });

        return response()->json($transformed);
    }

    /**
     * USR-05 — Detail lengkap transaksi.
     */
    public function show(Request $request, Transaction $transaction, MidtransService $midtrans): JsonResponse
    {
        if ($transaction->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        // Sync jika pending di Midtrans
        if ($transaction->payment_status === PaymentStatus::Pending && $transaction->payment_method === PaymentMethod::Midtrans) {
            $midtrans->checkTransactionStatus($transaction);
            $transaction->refresh();
        }

        $transaction->load([
            'service:id,name,slug,cover_image_url',
            'product:id,name,slug,primary_image_url,weight_estimate_kg',
            'documentations' => fn ($query) => $query->orderBy('created_at'),
        ]);

        $pipeline = collect(TransactionStatus::pipeline())->map(fn (TransactionStatus $st) => [
            'key' => $st->value,
            'label' => $st->label(),
            'is_completed' => $st->order() <= $transaction->status->order(),
            'is_current' => $st === $transaction->status,
        ]);

        $documentations = $transaction->documentations->map(function ($doc) {
            return [
                'id' => $doc->id,
                'stage' => $doc->stage?->value ?? $doc->stage,
                'type' => $doc->type,
                'file_url' => LandingController::formatMediaUrl($doc->file_url),
                'caption' => $doc->caption,
                'created_at' => $doc->created_at,
            ];
        });

        return response()->json([
            'transaction' => [
                'id' => $transaction->id,
                'transaction_code' => $transaction->transaction_code,
                'quantity' => $transaction->quantity,
                'unit_price' => $transaction->unit_price,
                'total_amount' => $transaction->total_amount,
                'status' => $transaction->status->value,
                'status_label' => $transaction->status->label(),
                'status_order' => $transaction->status->order(),
                'payment_status' => $transaction->payment_status->value,
                'payment_status_label' => $transaction->payment_status->label(),
                'payment_method' => $transaction->payment_method->value,
                'payment_method_label' => $transaction->payment_method->label(),
                'distribution_type' => $transaction->distribution_type->value,
                'distribution_type_label' => $transaction->distribution_type->label(),
                'distribution_location_note' => $transaction->distribution_location_note,
                'recipient_name' => $transaction->recipient_name,
                'recipient_phone' => $transaction->recipient_phone,
                'recipient_province' => $transaction->recipient_province,
                'recipient_city' => $transaction->recipient_city,
                'recipient_district' => $transaction->recipient_district,
                'recipient_address' => $transaction->recipient_address,
                'sohibul_names' => $transaction->sohibul_names,
                'manual_transfer_proof_url' => LandingController::formatMediaUrl($transaction->manual_transfer_proof_url),
                'rejected_reason' => $transaction->rejected_reason,
                'approved_at' => $transaction->approved_at,
                'midtrans_va_number' => $transaction->midtrans_va_number,
                'midtrans_payment_type' => $transaction->midtrans_payment_type,
                'midtrans_settlement_time' => $transaction->midtrans_settlement_time,
                'created_at' => $transaction->created_at,
                'service' => $transaction->service ? [
                    'id' => $transaction->service->id,
                    'name' => $transaction->service->name,
                    'slug' => $transaction->service->slug,
                    'cover_image_url' => LandingController::formatMediaUrl($transaction->service->cover_image_url),
                ] : null,
                'product' => $transaction->product ? [
                    'id' => $transaction->product->id,
                    'name' => $transaction->product->name,
                    'slug' => $transaction->product->slug,
                    'primary_image_url' => LandingController::formatMediaUrl($transaction->product->primary_image_url),
                    'weight_estimate_kg' => $transaction->product->weight_estimate_kg,
                ] : null,
                'documentations' => $documentations,
            ],
            'status_pipeline' => $pipeline,
            'bank_accounts' => $transaction->payment_method === PaymentMethod::ManualTransfer
                ? config('payment.bank_accounts', [])
                : [],
            'midtrans_client_key' => config('midtrans.client_key'),
        ]);
    }
}
