<?php

namespace App\Http\Controllers;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Transaction;
use App\Services\Midtrans\MidtransService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TransactionController extends Controller
{
    /**
     * USR-05 — Daftar transaksi milik user login, terbaru dulu.
     */
    public function index(Request $request, MidtransService $midtrans): Response
    {
        // Jika user baru kembali dari Midtrans redirect dengan order_id
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
            ->with(['service:id,name,slug', 'product:id,name,slug,primary_image_url'])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Transactions/Index', [
            'transactions' => $transactions,
        ]);
    }

    /**
     * USR-05 — Detail transaksi: invoice, pembayaran, timeline status, dokumentasi.
     */
    public function show(Request $request, Transaction $transaction, MidtransService $midtrans): Response
    {
        abort_unless($transaction->user_id === $request->user()->id, 403);

        // Jika transaksi masih pending di gateway, sync status terkini dari Midtrans
        if ($transaction->payment_status === PaymentStatus::Pending && $transaction->payment_method === PaymentMethod::Midtrans) {
            $midtrans->checkTransactionStatus($transaction);
            $transaction->refresh();
        }

        $transaction->load([
            'service:id,name,slug',
            'product:id,name,slug,primary_image_url,weight_estimate_kg',
            'documentations' => fn ($query) => $query->orderBy('created_at'),
        ]);

        return Inertia::render('Transactions/Show', [
            'transaction' => $transaction,
            'bank_accounts' => $transaction->payment_method === PaymentMethod::ManualTransfer
                ? config('payment.bank_accounts')
                : [],
            'midtrans_client_key' => config('midtrans.client_key'),
            'flash' => [
                'success' => session('success'),
            ],
        ]);
    }
}
