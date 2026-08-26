<script setup>
import StatusBadge from '@/Components/StatusBadge.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    transactions: {
        type: Object,
        required: true,
    },
});

const searchQuery = ref('');
const statusFilter = ref('all');

const rupiah = (v) =>
    new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(Number(v || 0));

const tanggal = (iso) =>
    new Date(iso).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });

const filteredTransactions = computed(() => {
    let list = props.transactions.data || [];

    if (statusFilter.value !== 'all') {
        list = list.filter((t) => t.payment_status === statusFilter.value);
    }

    if (searchQuery.value.trim()) {
        const q = searchQuery.value.toLowerCase();
        list = list.filter(
            (t) =>
                t.transaction_code.toLowerCase().includes(q) ||
                (t.product?.name && t.product.name.toLowerCase().includes(q)) ||
                (t.service?.name && t.service.name.toLowerCase().includes(q))
        );
    }

    return list;
});
</script>

<template>
    <Head title="Daftar Transaksi Saya" />

    <AuthenticatedLayout>
        <template #header>
            <div class="hidden md:flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-bold leading-tight text-gray-900">Riwayat Transaksi</h2>
                    <p class="text-xs text-gray-500">Pantau status pesanan, pembayaran, dan proses dokumentasi hewan Anda.</p>
                </div>
                <Link
                    href="/layanan"
                    class="inline-flex items-center gap-1.5 rounded-xl bg-brand-500 px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-brand-600 self-start sm:self-auto"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Pesan Hewan Baru
                </Link>
            </div>
        </template>

        <!-- ================= MOBILE APP NATIVE HEADER (Full Width Edge-to-Edge) ================= -->
        <div class="block md:hidden bg-gradient-to-b from-slate-900 via-zinc-900 to-zinc-900 text-white pt-4 pb-6 px-4 rounded-b-[2rem] shadow-xl relative overflow-hidden -mt-0 mb-4 w-full">
            <div class="flex items-center justify-between relative z-10 mb-3">
                <div class="flex items-center gap-2.5">
                    <div class="h-9 w-9 rounded-2xl bg-gradient-to-tr from-brand-600 to-amber-400 p-0.5 shadow-md flex items-center justify-center">
                        <div class="h-full w-full rounded-2xl bg-slate-900 flex items-center justify-center text-amber-300">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h1 class="text-base font-extrabold text-white leading-none">Riwayat Transaksi</h1>
                        <p class="text-[10px] text-zinc-400 mt-1">Status pesanan & dokumentasi</p>
                    </div>
                </div>

                <Link
                    href="/layanan"
                    class="inline-flex items-center gap-1 rounded-full bg-brand-500 hover:bg-brand-600 border border-brand-400/30 px-3 py-1.5 text-xs font-bold text-white shadow-sm transition active:scale-95"
                >
                    <span>+ Pesan</span>
                </Link>
            </div>

            <!-- Mobile Search Bar in Header -->
            <div class="relative z-10 mt-3">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Cari kode pesanan / hewan..."
                        class="w-full bg-white/10 backdrop-blur-md border border-white/15 rounded-2xl pl-10 pr-4 py-2.5 text-xs text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-amber-400/50 shadow-inner"
                    />
                </div>
            </div>
        </div>

        <div class="mx-auto max-w-6xl px-4 py-3 sm:py-8 sm:px-6 lg:px-8">
            <!-- Filter Pills / Chips -->
            <div class="mb-4 flex items-center gap-2 overflow-x-auto no-scrollbar pb-1">
                <button
                    type="button"
                    @click="statusFilter = 'all'"
                    class="rounded-xl px-3.5 py-1.5 text-xs font-bold transition whitespace-nowrap shadow-xs cursor-pointer"
                    :class="statusFilter === 'all' ? 'bg-brand-600 text-white' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50'"
                >
                    Semua Transaksi
                </button>
                <button
                    type="button"
                    @click="statusFilter = 'pending'"
                    class="rounded-xl px-3.5 py-1.5 text-xs font-bold transition whitespace-nowrap shadow-xs cursor-pointer"
                    :class="statusFilter === 'pending' ? 'bg-amber-500 text-white' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50'"
                >
                    Menunggu Pembayaran
                </button>
                <button
                    type="button"
                    @click="statusFilter = 'paid'"
                    class="rounded-xl px-3.5 py-1.5 text-xs font-bold transition whitespace-nowrap shadow-xs cursor-pointer"
                    :class="statusFilter === 'paid' ? 'bg-emerald-600 text-white' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50'"
                >
                    Lunas & Diproses
                </button>
            </div>

            <!-- Transaction List Cards -->
            <div v-if="filteredTransactions.length" class="space-y-3 sm:space-y-4">
                <div
                    v-for="t in filteredTransactions"
                    :key="t.id"
                    class="group relative overflow-hidden rounded-2xl sm:rounded-3xl border border-gray-200/80 bg-white p-4 sm:p-5 shadow-xs hover:shadow-md transition duration-200"
                >
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="font-mono text-[11px] font-bold text-gray-800 bg-gray-100 px-2 py-0.5 rounded-md">
                                {{ t.transaction_code }}
                            </span>
                            <span class="text-[11px] text-gray-400">
                                {{ tanggal(t.created_at) }}
                            </span>
                        </div>

                        <div class="flex items-center gap-1.5">
                            <StatusBadge :status="t.payment_status" />
                            <StatusBadge :status="t.status" />
                        </div>
                    </div>

                    <div class="mt-3 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <img
                                v-if="t.product?.primary_image_url"
                                :src="t.product.primary_image_url"
                                :alt="t.product.name"
                                class="h-14 w-14 shrink-0 rounded-xl object-cover border border-gray-100"
                            />
                            <div v-else class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-gray-100 text-[10px] text-gray-400">
                                Hewan
                            </div>

                            <div>
                                <h3 class="text-sm sm:text-base font-bold text-gray-900 group-hover:text-brand-600 transition">
                                    {{ t.product?.name }}
                                </h3>
                                <p class="text-[11px] text-gray-500">
                                    {{ t.service?.name }} · {{ t.quantity }} Ekor
                                </p>
                                <p class="text-[10px] text-gray-400 capitalize">
                                    {{ t.distribution_type === 'pt_yayasan' ? 'Disalurkan Yayasan' : 'Kirim Alamat Mandiri' }}
                                </p>
                            </div>
                        </div>

                        <div class="text-right shrink-0">
                            <span class="text-[9px] uppercase font-semibold text-gray-400 block">Total Bayar</span>
                            <p class="text-sm sm:text-base font-black text-brand-600">
                                {{ rupiah(t.total_amount) }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between">
                        <span class="text-[11px] font-semibold text-gray-500 capitalize">
                            {{ t.payment_method === 'midtrans' ? '⚡ Virtual Account / QRIS' : '🏦 Transfer Manual' }}
                        </span>
                        <Link
                            :href="route('transactions.show', t.transaction_code)"
                            class="inline-flex items-center gap-1 rounded-xl bg-gray-900 px-3 py-1.5 text-xs font-bold text-white transition hover:bg-brand-600 shadow-xs"
                        >
                            <span>Buka Detail</span>
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                            </svg>
                        </Link>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="transactions.links?.length > 3" class="flex flex-wrap justify-center gap-1 pt-6">
                    <template v-for="(link, i) in transactions.links" :key="i">
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            class="rounded-xl px-3.5 py-2 text-xs font-bold transition shadow-sm"
                            :class="link.active ? 'bg-brand-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200'"
                            v-html="link.label"
                        />
                        <span v-else class="px-3 py-2 text-xs text-gray-400" v-html="link.label" />
                    </template>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="rounded-3xl border border-dashed border-gray-300 bg-white p-12 text-center shadow-sm">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
                <h3 class="mt-3 text-base font-bold text-gray-900">Belum Ada Transaksi</h3>
                <p class="mt-1 text-xs text-gray-500">Anda belum melakukan pemesanan hewan qurban atau aqiqah.</p>
                <Link
                    href="/layanan"
                    class="mt-4 inline-flex items-center rounded-xl bg-brand-500 px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-brand-600"
                >
                    Lihat Pilihan Layanan &rarr;
                </Link>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
