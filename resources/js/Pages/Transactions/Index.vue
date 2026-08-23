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
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
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

        <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
            <!-- Filter & Search Bar -->
            <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between rounded-2xl border border-gray-200/80 bg-white p-4 shadow-sm">
                <div class="relative flex-1">
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Cari kode transaksi, produk, atau layanan..."
                        class="w-full rounded-xl border-gray-300 bg-gray-50/50 py-2 pl-9 pr-4 text-xs focus:border-brand-500 focus:bg-white focus:ring-brand-500"
                    />
                    <svg class="absolute left-3 top-2.5 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                <div class="flex items-center gap-2 overflow-x-auto pb-1 sm:pb-0">
                    <button
                        type="button"
                        @click="statusFilter = 'all'"
                        class="rounded-xl px-3 py-1.5 text-xs font-semibold transition whitespace-nowrap"
                        :class="statusFilter === 'all' ? 'bg-brand-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                    >
                        Semua
                    </button>
                    <button
                        type="button"
                        @click="statusFilter = 'pending'"
                        class="rounded-xl px-3 py-1.5 text-xs font-semibold transition whitespace-nowrap"
                        :class="statusFilter === 'pending' ? 'bg-amber-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                    >
                        Menunggu Bayar
                    </button>
                    <button
                        type="button"
                        @click="statusFilter = 'paid'"
                        class="rounded-xl px-3 py-1.5 text-xs font-semibold transition whitespace-nowrap"
                        :class="statusFilter === 'paid' ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                    >
                        Lunas
                    </button>
                </div>
            </div>

            <!-- Transaction List Cards -->
            <div v-if="filteredTransactions.length" class="space-y-4">
                <div
                    v-for="t in filteredTransactions"
                    :key="t.id"
                    class="group relative overflow-hidden rounded-2xl border border-gray-200/80 bg-white p-5 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-brand-500/40 hover:shadow-md"
                >
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-gray-100 pb-4">
                        <div class="flex items-center gap-3">
                            <span class="font-mono text-xs font-bold text-gray-700 bg-gray-100 px-2.5 py-1 rounded-lg">
                                {{ t.transaction_code }}
                            </span>
                            <span class="text-xs text-gray-400">
                                {{ tanggal(t.created_at) }}
                            </span>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <StatusBadge :status="t.payment_status" />
                            <StatusBadge :status="t.status" />
                        </div>
                    </div>

                    <div class="mt-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <img
                                v-if="t.product?.primary_image_url"
                                :src="t.product.primary_image_url"
                                :alt="t.product.name"
                                class="h-16 w-16 shrink-0 rounded-xl object-cover border border-gray-100"
                            />
                            <div v-else class="flex h-16 w-16 shrink-0 items-center justify-center rounded-xl bg-gray-100 text-xs text-gray-400">
                                Hewan
                            </div>

                            <div>
                                <h3 class="text-base font-bold text-gray-900 group-hover:text-brand-600 transition">
                                    {{ t.product?.name }}
                                </h3>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    Layanan: <span class="font-medium text-gray-700">{{ t.service?.name }}</span> · {{ t.quantity }} ekor
                                </p>
                                <p class="text-xs text-gray-400 mt-0.5 capitalize">
                                    Penyaluran: {{ t.distribution_type === 'pt_yayasan' ? 'Disalurkan Yayasan' : 'Kirim Alamat Mandiri' }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between sm:flex-col sm:items-end gap-2 border-t sm:border-t-0 pt-3 sm:pt-0 border-gray-100">
                            <div>
                                <span class="text-[10px] uppercase font-semibold text-gray-400 sm:text-right block">Total Transaksi</span>
                                <p class="text-lg font-black text-brand-600 sm:text-right">
                                    {{ rupiah(t.total_amount) }}
                                </p>
                            </div>

                            <Link
                                :href="route('transactions.show', t.transaction_code)"
                                class="inline-flex items-center gap-1.5 rounded-xl bg-gray-900 px-3.5 py-2 text-xs font-bold text-white transition hover:bg-brand-600 shadow-sm"
                            >
                                Detail & Pelacakan &rarr;
                            </Link>
                        </div>
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
