<script setup>
import StatusBadge from '@/Components/StatusBadge.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    transactions: Object,
    filters: Object,
});

const filterState = ref({
    payment_status: props.filters?.payment_status ?? '',
    payment_method: props.filters?.payment_method ?? '',
    status: props.filters?.status ?? '',
});

const applyFilters = () => {
    router.get('/admin/transaksi', Object.fromEntries(
        Object.entries(filterState.value).filter(([, v]) => v !== ''),
    ), { preserveState: true });
};

const rupiah = (v) =>
    new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(v);

const tanggal = (iso) =>
    new Date(iso).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });

// Setujui / Tolak
const approve = (t) => {
    if (confirm(`Setujui pembayaran ${t.transaction_code}?`)) {
        router.post(`/admin/transaksi/${t.transaction_code}/setujui`, {}, { preserveScroll: true });
    }
};

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

const canModerate = (t) =>
    t.payment_method === 'manual_transfer' && t.payment_status === 'pending';
</script>

<template>
    <Head title="Transaksi" />

    <AdminLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Manajemen Transaksi</h2>
        </template>

        <!-- Filter -->
        <div class="mb-4 flex flex-wrap gap-3">
            <select v-model="filterState.payment_status" class="rounded-md border-gray-300 text-sm" @change="applyFilters">
                <option value="">Semua Status Bayar</option>
                <option value="pending">Menunggu</option>
                <option value="paid">Lunas</option>
                <option value="rejected">Ditolak</option>
                <option value="expired">Kedaluwarsa</option>
                <option value="cancelled">Dibatalkan</option>
            </select>
            <select v-model="filterState.payment_method" class="rounded-md border-gray-300 text-sm" @change="applyFilters">
                <option value="">Semua Metode</option>
                <option value="midtrans">Midtrans</option>
                <option value="manual_transfer">Transfer Manual</option>
            </select>
            <select v-model="filterState.status" class="rounded-md border-gray-300 text-sm" @change="applyFilters">
                <option value="">Semua Tahap</option>
                <option value="menunggu">Menunggu</option>
                <option value="dibayar">Dibayar</option>
                <option value="hewan_disiapkan">Hewan Disiapkan</option>
                <option value="tersembelih">Tersembelih</option>
                <option value="didistribusikan">Didistribusikan</option>
            </select>
        </div>

        <div class="overflow-x-auto rounded-lg bg-white shadow-sm">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-left text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-4 py-3">Kode</th>
                        <th class="px-4 py-3">Pembeli</th>
                        <th class="px-4 py-3">Produk</th>
                        <th class="px-4 py-3">Total</th>
                        <th class="px-4 py-3">Metode</th>
                        <th class="px-4 py-3">Bayar</th>
                        <th class="px-4 py-3">Tahap</th>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-for="t in transactions.data" :key="t.id" class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-mono">
                            <Link :href="`/admin/transaksi/${t.transaction_code}`" class="text-indigo-600 hover:underline">
                                {{ t.transaction_code }}
                            </Link>
                        </td>
                        <td class="px-4 py-3">{{ t.user_name }}</td>
                        <td class="px-4 py-3">
                            {{ t.product_name }}
                            <span class="text-gray-400">· {{ t.service_name }}</span>
                        </td>
                        <td class="px-4 py-3">{{ rupiah(t.total_amount) }}</td>
                        <td class="px-4 py-3">{{ t.payment_method_label }}</td>
                        <td class="px-4 py-3"><StatusBadge :status="t.payment_status" /></td>
                        <td class="px-4 py-3"><StatusBadge :status="t.status" /></td>
                        <td class="px-4 py-3 text-gray-500">{{ tanggal(t.created_at) }}</td>
                        <td class="px-4 py-3">
                            <div v-if="canModerate(t)" class="flex gap-2">
                                <button
                                    class="rounded bg-green-600 px-2 py-1 text-xs text-white hover:bg-green-700 disabled:opacity-40"
                                    :disabled="!t.manual_transfer_proof_url"
                                    :title="t.manual_transfer_proof_url ? '' : 'Belum ada bukti transfer'"
                                    @click="approve(t)"
                                >
                                    Setujui
                                </button>
                                <button
                                    class="rounded bg-red-600 px-2 py-1 text-xs text-white hover:bg-red-700"
                                    @click="rejectTarget = t"
                                >
                                    Tolak
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="!transactions.data.length">
                        <td colspan="9" class="px-4 py-8 text-center text-gray-500">Belum ada transaksi.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div v-if="transactions.links?.length > 3" class="mt-4 flex flex-wrap gap-1">
            <template v-for="(link, i) in transactions.links" :key="i">
                <Link
                    v-if="link.url"
                    :href="link.url"
                    class="rounded px-3 py-1 text-sm"
                    :class="link.active ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'"
                    v-html="link.label"
                />
                <span v-else class="px-3 py-1 text-sm text-gray-400" v-html="link.label" />
            </template>
        </div>

        <!-- Modal tolak -->
        <div
            v-if="rejectTarget"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4"
            @click.self="rejectTarget = null"
        >
            <form class="w-full max-w-md space-y-4 rounded-lg bg-white p-6 shadow-lg" @submit.prevent="submitReject">
                <h3 class="font-semibold text-gray-900">
                    Tolak pembayaran {{ rejectTarget.transaction_code }}
                </h3>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Alasan penolakan</label>
                    <textarea
                        v-model="rejectForm.reason"
                        rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        required
                    />
                    <p v-if="rejectForm.errors.reason" class="mt-1 text-sm text-red-600">{{ rejectForm.errors.reason }}</p>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" class="rounded px-3 py-2 text-sm text-gray-600" @click="rejectTarget = null">
                        Batal
                    </button>
                    <button
                        type="submit"
                        class="rounded bg-red-600 px-3 py-2 text-sm text-white hover:bg-red-700"
                        :disabled="rejectForm.processing"
                    >
                        Tolak Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
