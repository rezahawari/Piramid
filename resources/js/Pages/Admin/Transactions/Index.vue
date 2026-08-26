<script setup>
import StatusBadge from '@/Components/StatusBadge.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    transactions: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
});

const filterState = ref({
    payment_status: props.filters?.payment_status ?? '',
    payment_method: props.filters?.payment_method ?? '',
    status: props.filters?.status ?? '',
});

const applyFilters = () => {
    router.get(
        '/admin/transaksi',
        Object.fromEntries(
            Object.entries(filterState.value).filter(([, v]) => v !== ''),
        ),
        { preserveState: true }
    );
};

const resetFilters = () => {
    filterState.value = {
        payment_status: '',
        payment_method: '',
        status: '',
    };
    applyFilters();
};

const rupiah = (v) =>
    new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(Number(v || 0));

const tanggal = (iso) =>
    new Date(iso).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });

// Setujui Pembayaran Manual
const approve = (t) => {
    if (confirm(`Apakah Anda yakin ingin menyetujui pembayaran ${t.transaction_code}?`)) {
        router.post(`/admin/transaksi/${t.transaction_code}/setujui`, {}, { preserveScroll: true });
    }
};

// Tolak Pembayaran
const rejectTarget = ref(null);
const rejectForm = useForm({ reason: '' });
const submitReject = () => {
    rejectForm.post(`/admin/transaksi/${rejectTarget.value.transaction_code}/tolak`, {
        preserveScroll: true,
        onSuccess: () => {
            rejectTarget.value = null;
            rejectForm.reset();
        },
    });
};

// Hapus Transaksi
const deleteTarget = ref(null);
const deleteForm = useForm({});
const submitDelete = () => {
    if (!deleteTarget.value) return;
    deleteForm.delete(`/admin/transaksi/${deleteTarget.value.transaction_code}`, {
        preserveScroll: true,
        onSuccess: () => {
            deleteTarget.value = null;
        },
    });
};

const canModerate = (t) =>
    t.payment_method === 'manual_transfer' && t.payment_status === 'pending';
</script>

<template>
    <Head title="Kelola Transaksi - Admin" />

    <AdminLayout>
        <!-- ================= MOBILE APP NATIVE TOP HEADER ================= -->
        <div class="block md:hidden bg-gradient-to-b from-slate-900 via-zinc-900 to-zinc-900 text-white pt-4 pb-6 px-4 -mx-3 -mt-4 rounded-b-[2rem] shadow-xl relative overflow-hidden mb-5">
            <div class="flex items-center justify-between relative z-10">
                <div>
                    <span class="text-[10px] text-zinc-400 font-bold uppercase tracking-wider block">Manajemen Pesanan</span>
                    <h2 class="text-base font-black text-white leading-tight">Daftar Transaksi</h2>
                </div>
                <span class="rounded-full bg-white/10 px-3 py-1 text-[11px] font-bold text-amber-300 border border-white/10">
                    {{ transactions.total ?? transactions.data.length }} Pesanan
                </span>
            </div>
        </div>

        <template #header>
            <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-bold leading-tight text-gray-900">Manajemen Transaksi</h2>
                    <p class="text-xs text-gray-500">Kelola status pembayaran, review bukti transfer, dan progres pengerjaan.</p>
                </div>
                <span class="text-xs font-semibold text-gray-500">
                    Total: <strong class="text-gray-900">{{ transactions.total ?? transactions.data.length }}</strong> transaksi
                </span>
            </div>
        </template>

        <!-- Flash Message Notification -->
        <div
            v-if="$page.props.flash?.success"
            class="mb-6 flex items-center gap-3 rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-sm text-emerald-800 shadow-sm"
        >
            <svg class="h-5 w-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span>{{ $page.props.flash.success }}</span>
        </div>

        <!-- Filter & Search Bar -->
        <div class="mb-5 rounded-2xl border border-gray-200/80 bg-white p-3.5 sm:p-4 shadow-xs">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                <div>
                    <select
                        v-model="filterState.payment_status"
                        class="w-full rounded-xl border-gray-300 bg-gray-50/50 py-2 text-xs focus:border-brand-500 focus:bg-white focus:ring-brand-500"
                        @change="applyFilters"
                    >
                        <option value="">Semua Status Pembayaran</option>
                        <option value="pending">Menunggu Pembayaran</option>
                        <option value="paid">Lunas (Paid)</option>
                        <option value="rejected">Ditolak</option>
                        <option value="expired">Kedaluwarsa</option>
                        <option value="cancelled">Dibatalkan</option>
                    </select>
                </div>

                <div>
                    <select
                        v-model="filterState.payment_method"
                        class="w-full rounded-xl border-gray-300 bg-gray-50/50 py-2 text-xs focus:border-brand-500 focus:bg-white focus:ring-brand-500"
                        @change="applyFilters"
                    >
                        <option value="">Semua Metode Bayar</option>
                        <option value="midtrans">Midtrans (Otomatis)</option>
                        <option value="manual_transfer">Transfer Bank Manual</option>
                    </select>
                </div>

                <div class="flex gap-2">
                    <select
                        v-model="filterState.status"
                        class="w-full rounded-xl border-gray-300 bg-gray-50/50 py-2 text-xs focus:border-brand-500 focus:bg-white focus:ring-brand-500"
                        @change="applyFilters"
                    >
                        <option value="">Semua Tahap Pengerjaan</option>
                        <option value="menunggu">Menunggu</option>
                        <option value="dibayar">Dibayar</option>
                        <option value="hewan_disiapkan">Hewan Disiapkan</option>
                        <option value="tersembelih">Penyembelihan</option>
                        <option value="didistribusikan">Pendistribusian</option>
                    </select>
                    
                    <button
                        type="button"
                        @click="resetFilters"
                        class="shrink-0 rounded-xl border border-gray-300 bg-white px-3 py-2 text-xs font-semibold text-gray-700 shadow-xs hover:bg-gray-50"
                    >
                        Reset
                    </button>
                </div>
            </div>
        </div>

        <!-- ================= MOBILE VIEW: NATIVE APP CARDS ================= -->
        <div class="block md:hidden space-y-3 mb-6">
            <div
                v-for="t in transactions.data"
                :key="t.id"
                class="rounded-2xl border border-gray-200/80 bg-white p-4 shadow-xs space-y-3"
            >
                <div class="flex items-center justify-between border-b border-gray-100 pb-2.5">
                    <Link
                        :href="route('admin.transactions.show', t.transaction_code)"
                        class="font-mono text-xs font-black text-brand-700 flex items-center gap-1"
                    >
                        <span>#{{ t.transaction_code }}</span>
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </Link>
                    <span class="text-[10px] text-gray-400 font-medium">{{ tanggal(t.created_at) }}</span>
                </div>

                <div class="flex gap-3">
                    <img
                        v-if="t.product_image_url"
                        :src="t.product_image_url"
                        :alt="t.product_name"
                        class="h-14 w-14 rounded-xl object-cover border border-gray-100 shrink-0"
                    />
                    <div class="flex-1 min-w-0">
                        <h4 class="text-xs font-bold text-gray-900 truncate">{{ t.product_name }}</h4>
                        <p class="text-[11px] text-gray-500">{{ t.service_name }} · {{ t.quantity }} ekor</p>
                        <p class="text-[11px] font-medium text-zinc-600 truncate mt-0.5">
                            👤 {{ t.user_name || 'Tamu' }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-1 border-t border-gray-100 text-xs">
                    <div>
                        <span class="text-[10px] text-gray-400 block">Total</span>
                        <span class="font-extrabold text-brand-600">{{ rupiah(t.total_amount) }}</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <StatusBadge :status="t.payment_status" />
                        <StatusBadge :status="t.status" />
                    </div>
                </div>

                <div class="pt-2 flex items-center gap-2">
                    <Link
                        :href="route('admin.transactions.show', t.transaction_code)"
                        class="flex-1 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-center py-2 text-xs font-bold transition shadow-xs"
                    >
                        Kelola Transaksi & Dokumentasi
                    </Link>
                </div>
            </div>
        </div>

        <!-- ================= DESKTOP VIEW: TABLE CARD ================= -->
        <div class="hidden md:block overflow-hidden rounded-2xl border border-gray-200/80 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="border-b border-gray-200 bg-gray-50/75 text-[11px] font-bold uppercase tracking-wider text-gray-500">
                        <tr>
                            <th class="px-5 py-3.5">Kode Transaksi</th>
                            <th class="px-5 py-3.5">Pelanggan</th>
                            <th class="px-5 py-3.5">Produk / Layanan</th>
                            <th class="px-5 py-3.5">Total Biaya</th>
                            <th class="px-5 py-3.5">Metode</th>
                            <th class="px-5 py-3.5">Status Bayar</th>
                            <th class="px-5 py-3.5">Tahap</th>
                            <th class="px-5 py-3.5">Tanggal</th>
                            <th class="px-5 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr
                            v-for="t in transactions.data"
                            :key="t.id"
                            class="hover:bg-gray-50/75 transition duration-150"
                        >
                            <!-- Kode Transaksi -->
                            <td class="px-5 py-4">
                                <Link
                                    :href="route('admin.transactions.show', t.transaction_code)"
                                    class="font-mono font-bold text-brand-600 hover:text-brand-700 hover:underline"
                                >
                                    {{ t.transaction_code }}
                                </Link>
                            </td>

                            <!-- Pelanggan -->
                            <td class="px-5 py-4">
                                <p class="font-bold text-gray-900">{{ t.user_name || 'Tamu' }}</p>
                                <p class="text-[11px] text-gray-400">{{ t.user_email || '-' }}</p>
                            </td>

                            <!-- Produk & Layanan -->
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <img
                                        v-if="t.product_image_url"
                                        :src="t.product_image_url"
                                        :alt="t.product_name"
                                        class="h-10 w-10 shrink-0 rounded-lg object-cover border border-gray-100"
                                    />
                                    <div>
                                        <p class="font-bold text-gray-900">{{ t.product_name }}</p>
                                        <p class="text-[11px] text-gray-500">{{ t.service_name }} · {{ t.quantity }}x</p>
                                    </div>
                                </div>
                            </td>

                            <!-- Total -->
                            <td class="px-5 py-4 font-bold text-gray-900 whitespace-nowrap">
                                {{ rupiah(t.total_amount) }}
                            </td>

                            <!-- Metode Bayar -->
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="rounded bg-gray-100 px-2 py-0.5 text-[11px] font-medium text-gray-700">
                                    {{ t.payment_method_label }}
                                </span>
                            </td>

                            <!-- Status Bayar -->
                            <td class="px-5 py-4 whitespace-nowrap">
                                <StatusBadge :status="t.payment_status" />
                            </td>

                            <!-- Tahap Pengerjaan -->
                            <td class="px-5 py-4 whitespace-nowrap">
                                <StatusBadge :status="t.status" />
                            </td>

                            <!-- Waktu -->
                            <td class="px-5 py-4 text-gray-400 whitespace-nowrap">
                                {{ tanggal(t.created_at) }}
                            </td>

                            <!-- Aksi -->
                            <td class="px-5 py-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- Tombol Detail -->
                                    <Link
                                        :href="route('admin.transactions.show', t.transaction_code)"
                                        class="inline-flex items-center rounded-lg bg-gray-100 px-2.5 py-1.5 text-xs font-semibold text-gray-700 transition hover:bg-brand-500 hover:text-white"
                                        title="Detail & Kelola Status"
                                    >
                                        Detail
                                    </Link>

                                    <!-- Tombol Validasi Manual -->
                                    <template v-if="canModerate(t)">
                                        <button
                                            type="button"
                                            class="inline-flex items-center rounded-lg bg-emerald-600 px-2.5 py-1.5 text-xs font-semibold text-white transition hover:bg-emerald-700 disabled:opacity-40"
                                            :disabled="!t.manual_transfer_proof_url"
                                            :title="t.manual_transfer_proof_url ? 'Setujui Pembayaran' : 'Belum ada bukti transfer'"
                                            @click="approve(t)"
                                        >
                                            Setujui
                                        </button>
                                        <button
                                            type="button"
                                            class="inline-flex items-center rounded-lg bg-amber-600 px-2.5 py-1.5 text-xs font-semibold text-white transition hover:bg-amber-700"
                                            title="Tolak Pembayaran"
                                            @click="rejectTarget = t"
                                        >
                                            Tolak
                                        </button>
                                    </template>

                                    <!-- Tombol Hapus Transaksi -->
                                    <button
                                        type="button"
                                        class="inline-flex items-center rounded-lg bg-rose-50 px-2.5 py-1.5 text-xs font-semibold text-rose-600 transition hover:bg-rose-600 hover:text-white"
                                        title="Hapus Transaksi"
                                        @click="deleteTarget = t"
                                    >
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!transactions.data.length">
                            <td colspan="9" class="px-5 py-12 text-center text-gray-400">
                                <svg class="mx-auto h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                                <p class="mt-2 text-sm font-semibold text-gray-700">Tidak ada transaksi ditemukan.</p>
                                <p class="text-xs text-gray-400">Coba ubah filter atau lakukan transaksi baru.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Bar -->
            <div v-if="transactions.links?.length > 3" class="border-t border-gray-100 bg-gray-50/50 px-5 py-3.5 flex flex-wrap justify-between items-center gap-2">
                <span class="text-xs text-gray-500">
                    Menampilkan total {{ transactions.total }} transaksi
                </span>
                <div class="flex flex-wrap gap-1">
                    <template v-for="(link, i) in transactions.links" :key="i">
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            class="rounded-lg px-3 py-1.5 text-xs font-semibold transition"
                            :class="link.active ? 'bg-brand-500 text-white shadow-sm' : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200'"
                            v-html="link.label"
                        />
                        <span v-else class="px-3 py-1.5 text-xs text-gray-400" v-html="link.label" />
                    </template>
                </div>
            </div>
        </div>

        <!-- Modal Tolak Pembayaran -->
        <div
            v-if="rejectTarget"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm"
            @click.self="rejectTarget = null"
        >
            <form class="w-full max-w-md space-y-4 rounded-2xl bg-white p-6 shadow-2xl" @submit.prevent="submitReject">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-amber-100 text-amber-700">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900">
                            Tolak Pembayaran
                        </h3>
                        <p class="font-mono text-xs text-gray-500">Invoice: {{ rejectTarget.transaction_code }}</p>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700">Alasan Penolakan</label>
                    <textarea
                        v-model="rejectForm.reason"
                        rows="3"
                        placeholder="Contoh: Bukti transfer tidak jelas / nominal tidak sesuai..."
                        class="mt-1 block w-full rounded-xl border-gray-300 text-xs shadow-sm focus:border-brand-500 focus:ring-brand-500"
                        required
                    ></textarea>
                    <p v-if="rejectForm.errors.reason" class="mt-1 text-xs text-rose-600">{{ rejectForm.errors.reason }}</p>
                </div>

                <div class="flex justify-end gap-2 border-t border-gray-100 pt-3">
                    <button
                        type="button"
                        class="rounded-xl border border-gray-300 bg-white px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50"
                        @click="rejectTarget = null"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        class="rounded-xl bg-rose-600 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-rose-700"
                        :disabled="rejectForm.processing"
                    >
                        {{ rejectForm.processing ? 'Menolak...' : 'Tolak Pembayaran' }}
                    </button>
                </div>
            </form>
        </div>

        <!-- Modal Konfirmasi Hapus Transaksi -->
        <div
            v-if="deleteTarget"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm"
            @click.self="deleteTarget = null"
        >
            <div class="w-full max-w-md space-y-4 rounded-2xl bg-white p-6 shadow-2xl">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-rose-100 text-rose-700">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900">
                            Hapus Transaksi?
                        </h3>
                        <p class="font-mono text-xs text-gray-500">Invoice: {{ deleteTarget.transaction_code }}</p>
                    </div>
                </div>

                <p class="text-xs text-gray-600 leading-relaxed">
                    Apakah Anda yakin ingin menghapus transaksi ini? Data transaksi beserta dokumentasi terkait akan dihapus secara permanen, dan kuantitas stok hewan akan dikembalikan secara otomatis.
                </p>

                <div class="flex justify-end gap-2 border-t border-gray-100 pt-3">
                    <button
                        type="button"
                        class="rounded-xl border border-gray-300 bg-white px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50"
                        @click="deleteTarget = null"
                    >
                        Batal
                    </button>
                    <button
                        type="button"
                        class="rounded-xl bg-rose-600 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-rose-700"
                        :disabled="deleteForm.processing"
                        @click="submitDelete"
                    >
                        {{ deleteForm.processing ? 'Menghapus...' : 'Ya, Hapus Transaksi' }}
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
