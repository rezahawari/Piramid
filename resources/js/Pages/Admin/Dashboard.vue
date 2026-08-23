<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    stats: {
        type: Object,
        required: true,
    },
    trends: {
        type: Object,
        required: true,
    },
    paymentStatusCounts: {
        type: Object,
        required: true,
    },
    transactionStatusCounts: {
        type: Object,
        required: true,
    },
    serviceBreakdown: {
        type: Array,
        required: true,
    },
    lowStockProducts: {
        type: Array,
        required: true,
    },
    recentTransactions: {
        type: Array,
        required: true,
    },
});

const formatRupiah = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(value || 0);
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const activeTab = ref('transactions'); // 'transactions' | 'revenue'

// SVG Area Chart calculations
const chartData = computed(() => {
    const values =
        activeTab.value === 'transactions'
            ? props.trends.transactions
            : props.trends.revenue;
    const max = Math.max(...values, 1);
    const min = 0;
    const height = 180;
    const width = 600;
    const padding = 20;

    const points = values.map((val, idx) => {
        const x = padding + (idx / (values.length - 1)) * (width - 2 * padding);
        const y = height - padding - ((val - min) / (max - min)) * (height - 2 * padding);
        return { x, y, val, label: props.trends.labels[idx] };
    });

    const pathD = points.reduce((acc, pt, idx) => {
        if (idx === 0) return `M ${pt.x} ${pt.y}`;
        // Smooth curved bezier line
        const prev = points[idx - 1];
        const cp1x = prev.x + (pt.x - prev.x) / 2;
        const cp1y = prev.y;
        const cp2x = prev.x + (pt.x - prev.x) / 2;
        const cp2y = pt.y;
        return `${acc} C ${cp1x} ${cp1y}, ${cp2x} ${cp2y}, ${pt.x} ${pt.y}`;
    }, '');

    const areaD = points.length
        ? `${pathD} L ${points[points.length - 1].x} ${height - padding} L ${points[0].x} ${height - padding} Z`
        : '';

    return { points, pathD, areaD, max, min };
});

const cards = computed(() => [
    {
        key: 'totalRevenue',
        label: 'Total Pendapatan Terverifikasi',
        value: formatRupiah(props.stats.totalRevenue),
        href: '/admin/transaksi',
        color: 'text-brand-600',
        bg: 'bg-brand-50',
        icon: 'money',
    },
    {
        key: 'paidTransactions',
        label: 'Transaksi Berhasil',
        value: `${props.stats.paidTransactions} / ${props.stats.transactions}`,
        href: '/admin/transaksi',
        color: 'text-emerald-600',
        bg: 'bg-emerald-50',
        icon: 'check-circle',
    },
    {
        key: 'pendingManualPayments',
        label: 'Perlu Verifikasi Manual',
        value: props.stats.pendingManualPayments,
        href: '/admin/transaksi',
        color: 'text-amber-600',
        bg: 'bg-amber-50',
        icon: 'clock',
        badge: props.stats.pendingManualPayments > 0 ? 'Perlu Aksi' : null,
    },
    {
        key: 'products',
        label: 'Katalog Produk Hewan',
        value: `${props.stats.products} Item`,
        href: '/admin/produk',
        color: 'text-blue-600',
        bg: 'bg-blue-50',
        icon: 'cube',
    },
]);

const statusLabels = {
    pending: { label: 'Pending', color: 'bg-gray-100 text-gray-800' },
    penyembelihan: { label: 'Penyembelihan', color: 'bg-amber-100 text-amber-800' },
    pencacahan: { label: 'Pencacahan', color: 'bg-indigo-100 text-indigo-800' },
    pengemasan: { label: 'Pengemasan', color: 'bg-purple-100 text-purple-800' },
    pendistribusian: { label: 'Pendistribusian', color: 'bg-blue-100 text-blue-800' },
    selesai: { label: 'Selesai', color: 'bg-emerald-100 text-emerald-800' },
    dibatalkan: { label: 'Dibatalkan', color: 'bg-rose-100 text-rose-800' },
};

const paymentStatusLabels = {
    pending: { label: 'Menunggu Pembayaran', color: 'text-amber-600' },
    paid: { label: 'Lunas / Berhasil', color: 'text-emerald-600' },
    failed: { label: 'Gagal', color: 'text-rose-600' },
    expired: { label: 'Kadaluarsa', color: 'text-gray-500' },
};
</script>

<template>
    <Head title="Dashboard Admin" />

    <AdminLayout>
        <template #header>
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-bold leading-tight text-gray-900">Dashboard Utama</h2>
                    <p class="text-xs text-gray-500">Ringkasan performa dan aktivitas transaksi terkini.</p>
                </div>
                <div class="flex items-center gap-2">
                    <Link
                        :href="route('admin.produk.create')"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-brand-500 px-3.5 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-brand-600"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Hewan
                    </Link>
                    <Link
                        :href="route('admin.layanan.create')"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-xs font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50"
                    >
                        Tambah Layanan
                    </Link>
                </div>
            </div>
        </template>

        <!-- KPI Statistic Cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <Link
                v-for="card in cards"
                :key="card.key"
                :href="card.href"
                class="group relative overflow-hidden rounded-xl border border-gray-100 bg-white p-5 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-md"
            >
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wider text-gray-500">{{ card.label }}</p>
                        <p class="mt-2 text-2xl font-bold tracking-tight text-gray-900" :class="card.color">
                            {{ card.value }}
                        </p>
                    </div>
                    <span
                        v-if="card.badge"
                        class="inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-800 animate-pulse"
                    >
                        {{ card.badge }}
                    </span>
                </div>
                <div class="mt-3 flex items-center text-xs font-medium text-gray-400 group-hover:text-brand-500">
                    <span>Lihat rincian</span>
                    <svg class="ml-1 h-3.5 w-3.5 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </Link>
        </div>

        <!-- Middle Section: Interactive Charts & Breakdown -->
        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Left 2 Cols: Main Trend Chart -->
            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm lg:col-span-2">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between border-b border-gray-100 pb-4">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Tren 7 Hari Terakhir</h3>
                        <p class="text-xs text-gray-500">Aktivitas volume transaksi dan omset harian</p>
                    </div>
                    <div class="inline-flex rounded-lg bg-gray-100 p-1 text-xs">
                        <button
                            type="button"
                            @click="activeTab = 'transactions'"
                            class="rounded-md px-3 py-1.5 font-medium transition"
                            :class="activeTab === 'transactions' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900'"
                        >
                            Total Pesanan
                        </button>
                        <button
                            type="button"
                            @click="activeTab = 'revenue'"
                            class="rounded-md px-3 py-1.5 font-medium transition"
                            :class="activeTab === 'revenue' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900'"
                        >
                            Pendapatan (Rp)
                        </button>
                    </div>
                </div>

                <!-- Custom Interactive SVG Chart -->
                <div class="mt-6">
                    <div class="relative w-full overflow-hidden">
                        <svg viewBox="0 0 600 180" class="w-full h-48 overflow-visible">
                            <defs>
                                <linearGradient id="chartGradient" x1="0%" y1="0%" x2="0%" y2="100%">
                                    <stop offset="0%" stop-color="#6a1918" stop-opacity="0.25" />
                                    <stop offset="100%" stop-color="#6a1918" stop-opacity="0.0" />
                                </linearGradient>
                            </defs>

                            <!-- Grid Lines -->
                            <line x1="20" y1="20" x2="580" y2="20" stroke="#f3f4f6" stroke-dasharray="4 4" stroke-width="1" />
                            <line x1="20" y1="80" x2="580" y2="80" stroke="#f3f4f6" stroke-dasharray="4 4" stroke-width="1" />
                            <line x1="20" y1="140" x2="580" y2="140" stroke="#f3f4f6" stroke-dasharray="4 4" stroke-width="1" />
                            <line x1="20" y1="160" x2="580" y2="160" stroke="#e5e7eb" stroke-width="1" />

                            <!-- Gradient Area Fill -->
                            <path :d="chartData.areaD" fill="url(#chartGradient)" />

                            <!-- Trend Line Stroke -->
                            <path :d="chartData.pathD" fill="none" stroke="#6a1918" stroke-width="2.5" stroke-linecap="round" />

                            <!-- Data Points -->
                            <g v-for="(pt, idx) in chartData.points" :key="idx">
                                <circle
                                    :cx="pt.x"
                                    :cy="pt.y"
                                    r="4.5"
                                    class="fill-brand-500 stroke-white stroke-2 transition hover:r-6 cursor-pointer"
                                />
                                <text
                                    :x="pt.x"
                                    :y="pt.y - 10"
                                    text-anchor="middle"
                                    class="text-[10px] font-bold fill-gray-700"
                                >
                                    {{ activeTab === 'transactions' ? pt.val : (pt.val > 0 ? (pt.val / 1000000).toFixed(1) + 'jt' : '0') }}
                                </text>
                                <text
                                    :x="pt.x"
                                    y="176"
                                    text-anchor="middle"
                                    class="text-[10px] fill-gray-400 font-medium"
                                >
                                    {{ pt.label }}
                                </text>
                            </g>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Right 1 Col: Status Transaksi & Distribusi -->
            <div class="space-y-6">
                <!-- Status Tahapan Transaksi -->
                <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                    <h3 class="text-base font-bold text-gray-900">Progres Tahapan</h3>
                    <p class="text-xs text-gray-500">Status pengerjaan & dokumentasi hewan</p>

                    <div class="mt-4 space-y-3">
                        <div
                            v-for="(count, statusKey) in transactionStatusCounts"
                            :key="statusKey"
                            class="flex items-center justify-between"
                        >
                            <span class="text-xs font-medium text-gray-600 capitalize">
                                {{ statusLabels[statusKey]?.label || statusKey }}
                            </span>
                            <div class="flex items-center gap-2">
                                <span
                                    class="inline-flex rounded-full px-2 py-0.5 text-xs font-bold"
                                    :class="statusLabels[statusKey]?.color || 'bg-gray-100 text-gray-700'"
                                >
                                    {{ count }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Distribusi Status Pembayaran -->
                <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                    <h3 class="text-base font-bold text-gray-900">Status Pembayaran</h3>
                    <div class="mt-3 grid grid-cols-2 gap-3">
                        <div
                            v-for="(count, payKey) in paymentStatusCounts"
                            :key="payKey"
                            class="rounded-lg bg-gray-50 p-3"
                        >
                            <p class="text-xs text-gray-500 capitalize">{{ paymentStatusLabels[payKey]?.label }}</p>
                            <p class="mt-1 text-lg font-bold" :class="paymentStatusLabels[payKey]?.color">
                                {{ count }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Section: Stok Menipis & Transaksi Terkini -->
        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Left 2 Cols: Transaksi Terbaru -->
            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm lg:col-span-2">
                <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Transaksi Terbaru</h3>
                        <p class="text-xs text-gray-500">Pesanan qurban & aqiqah yang baru masuk</p>
                    </div>
                    <Link
                        href="/admin/transaksi"
                        class="text-xs font-semibold text-brand-600 hover:text-brand-700 hover:underline"
                    >
                        Lihat Semua Transaksi &rarr;
                    </Link>
                </div>

                <div class="mt-4 overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-gray-200 text-gray-400 uppercase tracking-wider">
                                <th class="py-2.5">Kode Transaksi</th>
                                <th class="py-2.5">Pelanggan</th>
                                <th class="py-2.5">Produk</th>
                                <th class="py-2.5">Total</th>
                                <th class="py-2.5">Status Bayar</th>
                                <th class="py-2.5 text-right">Waktu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr
                                v-for="trx in recentTransactions"
                                :key="trx.id"
                                class="hover:bg-gray-50/75 transition"
                            >
                                <td class="py-3 font-semibold text-gray-900">
                                    <Link :href="`/admin/transaksi/${trx.transaction_code}`" class="hover:text-brand-600 hover:underline">
                                        {{ trx.transaction_code }}
                                    </Link>
                                </td>
                                <td class="py-3 text-gray-700">
                                    <p class="font-medium">{{ trx.user?.name || trx.recipient_name }}</p>
                                    <p class="text-[10px] text-gray-400">{{ trx.user?.email }}</p>
                                </td>
                                <td class="py-3 text-gray-600">
                                    {{ trx.product?.name || trx.service?.name }} ({{ trx.quantity }}x)
                                </td>
                                <td class="py-3 font-semibold text-gray-900">
                                    {{ formatRupiah(trx.total_amount) }}
                                </td>
                                <td class="py-3">
                                    <span
                                        class="inline-flex rounded-full px-2 py-0.5 text-[10px] font-semibold"
                                        :class="trx.payment_status === 'paid' ? 'bg-emerald-100 text-emerald-800' : (trx.payment_status === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800')"
                                    >
                                        {{ trx.payment_status }}
                                    </span>
                                </td>
                                <td class="py-3 text-right text-gray-400 whitespace-nowrap">
                                    {{ formatDate(trx.created_at) }}
                                </td>
                            </tr>
                            <tr v-if="!recentTransactions.length">
                                <td colspan="6" class="py-6 text-center text-gray-400">
                                    Belum ada data transaksi.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Right 1 Col: Peringatan Stok Menipis & Pembagian Layanan -->
            <div class="space-y-6">
                <!-- Stok Rendah Alert -->
                <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base font-bold text-gray-900">Peringatan Stok</h3>
                        <span class="rounded-full bg-rose-50 px-2 py-0.5 text-xs font-semibold text-rose-600">
                            Stok &le; 5
                        </span>
                    </div>
                    <p class="text-xs text-gray-500">Produk yang membutuhkan restock segera</p>

                    <div class="mt-4 divide-y divide-gray-100">
                        <div
                            v-for="prod in lowStockProducts"
                            :key="prod.id"
                            class="flex items-center justify-between py-2.5"
                        >
                            <div class="flex items-center gap-3">
                                <img
                                    v-if="prod.primary_image_url"
                                    :src="prod.primary_image_url"
                                    :alt="prod.name"
                                    class="h-9 w-9 rounded-lg object-cover border border-gray-100"
                                />
                                <div>
                                    <p class="text-xs font-semibold text-gray-800">{{ prod.name }}</p>
                                    <p class="text-[10px] text-gray-400">{{ formatRupiah(prod.price) }}</p>
                                </div>
                            </div>
                            <span
                                class="inline-flex rounded-full px-2 py-0.5 text-xs font-bold"
                                :class="prod.stock === 0 ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700'"
                            >
                                Sisa {{ prod.stock }}
                            </span>
                        </div>
                        <div v-if="!lowStockProducts.length" class="py-4 text-center text-xs text-gray-400">
                            Semua stok hewan dalam jumlah aman.
                        </div>
                    </div>
                </div>

                <!-- Pembagian Layanan -->
                <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                    <h3 class="text-base font-bold text-gray-900">Pesanan per Layanan</h3>
                    <div class="mt-4 space-y-2">
                        <div
                            v-for="svc in serviceBreakdown"
                            :key="svc.name"
                            class="flex items-center justify-between text-xs"
                        >
                            <span class="font-medium text-gray-600">{{ svc.name }}</span>
                            <span class="font-bold text-gray-900">{{ svc.count }} pesanan</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
