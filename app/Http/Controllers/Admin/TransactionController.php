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
                'user_email' => $t->user?->email,
                'service_name' => $t->service?->name,
                'product_name' => $t->product?->name,
                'product_image_url' => $t->product?->primary_image_url,
                'quantity' => $t->quantity,
                'total_amount' => (float) $t->total_amount,
                'distribution_type' => $t->distribution_type->value,
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
            $transaction->status = TransactionStatus::Dibayar;
        }

        $transaction->save();

        // Kirim Push Notification otomatis ke HP user
        if ($transaction->user) {
            try {
                app(\App\Services\Notification\WebPushService::class)->sendToUser(
                    $transaction->user,
                    '✅ Pembayaran Disetujui!',
                    "Pembayaran pesanan #{$transaction->transaction_code} telah diverifikasi lunas. Hewan Anda siap disiapkan.",
                    route('transactions.show', $transaction->transaction_code)
                );
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Gagal push notif approve: ' . $e->getMessage());
            }
        }

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

        // Kirim Push Notification otomatis penolakan ke HP user
        if ($transaction->user) {
            try {
                app(\App\Services\Notification\WebPushService::class)->sendToUser(
                    $transaction->user,
                    '⚠️ Status Pembayaran Pesanan',
                    "Bukti pembayaran pesanan #{$transaction->transaction_code} ditolak: {$validated['reason']}",
                    route('transactions.show', $transaction->transaction_code)
                );
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Gagal push notif reject: ' . $e->getMessage());
            }
        }

        return back()->with('success', "Pembayaran {$transaction->transaction_code} ditolak.");
    }

    public function updateDistributionNote(Request $request, Transaction $transaction): RedirectResponse
    {
        $validated = $request->validate([
            'distribution_location_note' => ['nullable', 'string', 'max:500'],
        ]);

        $transaction->update([
            'distribution_location_note' => $validated['distribution_location_note'],
        ]);

        // Kirim Push Notif jika admin mengupdate info lokasi penyaluran
        if ($transaction->user && !empty($validated['distribution_location_note'])) {
            try {
                app(\App\Services\Notification\WebPushService::class)->sendToUser(
                    $transaction->user,
                    '📍 Info Penyaluran Daging!',
                    "Lokasi penyaluran pesanan #{$transaction->transaction_code}: {$validated['distribution_location_note']}",
                    route('transactions.show', $transaction->transaction_code)
                );
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Gagal push notif note: ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Keterangan lokasi penyaluran berhasil diperbarui.');
    }

    public function destroy(Transaction $transaction): RedirectResponse
    {
        DB::transaction(function () use ($transaction): void {
            // Jika transaksi masih berstatus pending/dibayar, kembalikan kuantitas stok produk
            if (in_array($transaction->payment_status, [PaymentStatus::Pending, PaymentStatus::Paid], true) && $transaction->product) {
                $transaction->product->increment('stock', $transaction->quantity);
            }

            // Hapus relasi dokumentasi jika ada
            $transaction->documentations()->delete();

            $transaction->delete();
        });

        return redirect()
            ->route('admin.transactions.index')
            ->with('success', "Transaksi {$transaction->transaction_code} berhasil dihapus.");
    }
}
