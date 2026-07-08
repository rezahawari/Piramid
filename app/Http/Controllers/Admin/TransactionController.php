<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\TransactionStatus;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class TransactionController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = [
            'payment_status' => $request->query('payment_status'),
            'payment_method' => $request->query('payment_method'),
            'status' => $request->query('status'),
        ];

        $transactions = Transaction::query()
            ->with(['user:id,name', 'service:id,name', 'product:id,name'])
            ->when($filters['payment_status'], fn ($q, $v) => $q->where('payment_status', $v))
            ->when($filters['payment_method'], fn ($q, $v) => $q->where('payment_method', $v))
            ->when($filters['status'], fn ($q, $v) => $q->where('status', $v))
            ->latest()
            ->paginate(15)
            ->withQueryString()
            ->through(fn (Transaction $t) => [
                'id' => $t->id,
                'transaction_code' => $t->transaction_code,
                'user_name' => $t->user?->name,
                'service_name' => $t->service?->name,
                'product_name' => $t->product?->name,
                'total_amount' => (float) $t->total_amount,
                'payment_method' => $t->payment_method->value,
                'payment_method_label' => $t->payment_method->label(),
                'payment_status' => $t->payment_status->value,
                'status' => $t->status->value,
                'manual_transfer_proof_url' => $t->manual_transfer_proof_url,
                'created_at' => $t->created_at?->toIso8601String(),
            ]);

        return Inertia::render('Admin/Transactions/Index', [
            'transactions' => $transactions,
            'filters' => $filters,
        ]);
    }

    public function approve(Transaction $transaction): RedirectResponse
    {
        abort_unless(
            $transaction->payment_method === PaymentMethod::ManualTransfer
                && $transaction->payment_status === PaymentStatus::Pending
                && $transaction->manual_transfer_proof_url,
            422,
            'Transaksi ini tidak dapat disetujui.',
        );

        $transaction->fill([
            'payment_status' => PaymentStatus::Paid,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        if ($transaction->status === TransactionStatus::Menunggu) {
            $transaction->status = TransactionStatus::HewanDisiapkan;
        }

        $transaction->save();

        return back()->with('success', "Pembayaran {$transaction->transaction_code} disetujui.");
    }

    public function reject(Request $request, Transaction $transaction): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        abort_unless(
            $transaction->payment_method === PaymentMethod::ManualTransfer
                && $transaction->payment_status === PaymentStatus::Pending,
            422,
            'Transaksi ini tidak dapat ditolak.',
        );

        DB::transaction(function () use ($transaction, $validated): void {
            $transaction->fill([
                'payment_status' => PaymentStatus::Rejected,
                'rejected_reason' => $validated['reason'],
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ])->save();

            // Return the reserved stock to the product.
            $transaction->product?->increment('stock', $transaction->quantity);
        });

        return back()->with('success', "Pembayaran {$transaction->transaction_code} ditolak.");
    }
}
