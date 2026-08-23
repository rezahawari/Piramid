<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\TransactionStatus;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Service;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        // 1. Ringkasan Metrik Utama (Cards)
        $totalRevenue = Transaction::query()
            ->where('payment_status', PaymentStatus::Paid->value)
            ->sum('total_amount');

        $totalPaidTransactions = Transaction::query()
            ->where('payment_status', PaymentStatus::Paid->value)
            ->count();

        $stats = [
            'totalRevenue' => (float) $totalRevenue,
            'services' => Service::count(),
            'products' => Product::count(),
            'transactions' => Transaction::count(),
            'users' => User::where('role', '!=', 'admin')->count(),
            'pendingManualPayments' => Transaction::query()
                ->where('payment_method', PaymentMethod::ManualTransfer->value)
                ->where('payment_status', PaymentStatus::Pending->value)
                ->count(),
            'paidTransactions' => $totalPaidTransactions,
        ];

        // 2. Data Tren Transaksi 7 Hari Terakhir
        $chartDates = [];
        $dailyTransactions = [];
        $dailyRevenue = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $formattedDate = $date->format('d M');
            $chartDates[] = $formattedDate;

            $dayCount = Transaction::whereDate('created_at', $date->toDateString())->count();
            $dayRevenue = Transaction::whereDate('created_at', $date->toDateString())
                ->where('payment_status', PaymentStatus::Paid->value)
                ->sum('total_amount');

            $dailyTransactions[] = $dayCount;
            $dailyRevenue[] = (float) $dayRevenue;
        }

        // 3. Distribusi Status Pembayaran & Transaksi
        $paymentStatusCounts = [
            'pending' => Transaction::where('payment_status', PaymentStatus::Pending->value)->count(),
            'paid' => Transaction::where('payment_status', PaymentStatus::Paid->value)->count(),
            'failed' => Transaction::where('payment_status', PaymentStatus::Failed->value)->count(),
            'expired' => Transaction::where('payment_status', PaymentStatus::Expired->value)->count(),
        ];

        $transactionStatusCounts = [
            'pending' => Transaction::where('status', TransactionStatus::Pending->value)->count(),
            'penyembelihan' => Transaction::where('status', TransactionStatus::Penyembelihan->value)->count(),
            'pencacahan' => Transaction::where('status', TransactionStatus::Pencacahan->value)->count(),
            'pengemasan' => Transaction::where('status', TransactionStatus::Pengemasan->value)->count(),
            'pendistribusian' => Transaction::where('status', TransactionStatus::Pendistribusian->value)->count(),
            'selesai' => Transaction::where('status', TransactionStatus::Selesai->value)->count(),
            'dibatalkan' => Transaction::where('status', TransactionStatus::Dibatalkan->value)->count(),
        ];

        // 4. Performa Transaksi per Layanan
        $serviceBreakdown = Service::withCount('transactions')
            ->get(['id', 'name'])
            ->map(function ($service) {
                return [
                    'name' => $service->name,
                    'count' => $service->transactions_count,
                ];
            });

        // 5. Produk dengan Stok Menipis (<= 5)
        $lowStockProducts = Product::where('stock', '<=', 5)
            ->orderBy('stock', 'asc')
            ->take(5)
            ->get(['id', 'name', 'stock', 'price', 'primary_image_url']);

        // 6. Transaksi Terbaru
        $recentTransactions = Transaction::with(['user:id,name,email', 'service:id,name', 'product:id,name'])
            ->latest()
            ->take(6)
            ->get();

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'trends' => [
                'labels' => $chartDates,
                'transactions' => $dailyTransactions,
                'revenue' => $dailyRevenue,
            ],
            'paymentStatusCounts' => $paymentStatusCounts,
            'transactionStatusCounts' => $transactionStatusCounts,
            'serviceBreakdown' => $serviceBreakdown,
            'lowStockProducts' => $lowStockProducts,
            'recentTransactions' => $recentTransactions,
        ]);
    }
}
