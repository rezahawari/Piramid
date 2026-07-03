<?php

namespace App\Http\Controllers;

use App\Enums\PaymentMethod;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TransactionController extends Controller
{
    /**
     * USR-05 — Daftar transaksi milik user login, terbaru dulu.
     */
    public function index(Request $request): Response
    {
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
    public function show(Request $request, Transaction $transaction): Response
    {
        abort_unless($transaction->user_id === $request->user()->id, 403);

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
