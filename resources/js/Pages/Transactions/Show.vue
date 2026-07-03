<script setup>
import DocumentationGallery from '@/Components/DocumentationGallery.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed, onMounted } from 'vue';

const props = defineProps({
    transaction: Object,
    bank_accounts: Array,
    midtrans_client_key: String,
    flash: Object,
});

const stages = [
    { value: 'menunggu', label: 'Menunggu' },
    { value: 'dibayar', label: 'Dibayar' },
    { value: 'hewan_disiapkan', label: 'Hewan Disiapkan' },
    { value: 'tersembelih', label: 'Tersembelih' },
    { value: 'didistribusikan', label: 'Didistribusikan' },
];

const currentStageIndex = computed(() =>
    stages.findIndex((s) => s.value === props.transaction.status),
);

const rupiah = (v) =>
    new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(v);

const tanggal = (iso) =>
    new Date(iso).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' });

const isPendingManual = computed(
    () => props.transaction.payment_method === 'manual_transfer' && props.transaction.payment_status === 'pending',
);
const isPendingMidtrans = computed(
    () => props.transaction.payment_method === 'midtrans' && props.transaction.payment_status === 'pending',
);
const isFailed = computed(() =>
    ['rejected', 'expired', 'cancelled'].includes(props.transaction.payment_status),
);

// Bukti transfer manual
const proofForm = useForm({ proof: null });
const submitProof = () =>
    proofForm.post(`/pembayaran/${props.transaction.transaction_code}/bukti-transfer`, {
        forceFormData: true,
        onSuccess: () => proofForm.reset(),
    });

// Midtrans Snap
onMounted(() => {
    if (props.midtrans_client_key && !document.getElementById('midtrans-snap-js')) {
        const s = document.createElement('script');
        s.id = 'midtrans-snap-js';
        s.src = 'https://app.sandbox.midtrans.com/snap/snap.js';
        s.setAttribute('data-client-key', props.midtrans_client_key);
        document.body.appendChild(s);
    }
});

const paying = useForm({});
const payNow = async () => {
    const res = await window.axios.post(
        `/pembayaran/${props.transaction.transaction_code}/snap-token`,
    );
    const token = res.data.snap_token;
    if (window.snap && token) {
        window.snap.pay(token, {
            onSuccess: () => window.location.reload(),
            onPending: () => window.location.reload(),
        });
    }
};

const failLabel = {
    rejected: 'Pembayaran ditolak',
    expired: 'Pembayaran kedaluwarsa',
    cancelled: 'Pembayaran dibatalkan',
};
</script>

<template>
    <Head :title="`Transaksi ${transaction.transaction_code}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-wrap items-center justify-between gap-2">
                <h2 class="font-mono text-xl font-semibold leading-tight text-gray-800">
                    {{ transaction.transaction_code }}
                </h2>
                <div class="flex gap-2">
                    <StatusBadge :status="transaction.payment_status" />
                    <StatusBadge :status="transaction.status" />
                </div>
            </div>
        </template>

        <div class="mx-auto max-w-4xl space-y-6 px-4 py-8 sm:px-6">
            <div
                v-if="flash?.success"
                class="rounded-lg bg-green-50 p-4 text-sm text-green-700"
            >
                {{ flash.success }}
            </div>

            <!-- Banner gagal bayar -->
            <div v-if="isFailed" class="rounded-lg bg-red-50 p-4">
                <p class="font-medium text-red-700">{{ failLabel[transaction.payment_status] }}</p>
                <p v-if="transaction.rejected_reason" class="mt-1 text-sm text-red-600">
                    Alasan: {{ transaction.rejected_reason }}
                </p>
            </div>

            <!-- Invoice -->
            <section class="rounded-lg bg-white p-6 shadow-sm">
                <h3 class="mb-4 font-semibold text-gray-900">Invoice</h3>
                <dl class="grid gap-x-8 gap-y-2 text-sm sm:grid-cols-2">
                    <div class="flex justify-between"><dt class="text-gray-500">Tanggal</dt><dd>{{ tanggal(transaction.created_at) }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Layanan</dt><dd>{{ transaction.service?.name }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Produk</dt><dd>{{ transaction.product?.name }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Jumlah</dt><dd>{{ transaction.quantity }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Harga Satuan</dt><dd>{{ rupiah(transaction.unit_price) }}</dd></div>
                    <div class="flex justify-between font-semibold"><dt class="text-gray-500">Total</dt><dd>{{ rupiah(transaction.total_amount) }}</dd></div>
                </dl>

                <div class="mt-4 border-t pt-4 text-sm">
                    <p class="font-medium text-gray-700">Penyaluran</p>
                    <p v-if="transaction.distribution_type === 'pt_yayasan'" class="text-gray-600">
                        Disalurkan oleh PT/Yayasan kepada penerima manfaat terdaftar.
                    </p>
                    <div v-else class="text-gray-600">
                        <p>{{ transaction.recipient_name }} · {{ transaction.recipient_phone }}</p>
                        <p>
                            {{ transaction.recipient_address }}, {{ transaction.recipient_district }},
                            {{ transaction.recipient_city }}, {{ transaction.recipient_province }}
                        </p>
                    </div>
                </div>
            </section>

            <!-- Pembayaran Midtrans -->
            <section v-if="isPendingMidtrans" class="rounded-lg bg-white p-6 shadow-sm">
                <h3 class="mb-2 font-semibold text-gray-900">Pembayaran</h3>
                <p class="mb-4 text-sm text-gray-600">
                    Selesaikan pembayaran melalui Virtual Account, E-Wallet, atau gerai retail.
                </p>
                <PrimaryButton :disabled="paying.processing" @click="payNow">Bayar Sekarang</PrimaryButton>
            </section>

            <!-- Pembayaran manual -->
            <section v-if="isPendingManual" class="rounded-lg bg-white p-6 shadow-sm">
                <h3 class="mb-2 font-semibold text-gray-900">Transfer Bank Manual</h3>
                <p class="mb-3 text-sm text-gray-600">Transfer ke salah satu rekening berikut:</p>
                <ul class="mb-4 space-y-2">
                    <li v-for="acc in bank_accounts" :key="acc.account_number" class="rounded border border-gray-200 p-3 text-sm">
                        <span class="font-semibold">{{ acc.bank }}</span>
                        — <span class="font-mono">{{ acc.account_number }}</span>
                        a.n. {{ acc.account_name }}
                    </li>
                </ul>

                <div v-if="transaction.manual_transfer_proof_url" class="mb-4 text-sm">
                    <p class="text-gray-700">Bukti transfer terkirim — menunggu verifikasi admin.</p>
                    <a :href="transaction.manual_transfer_proof_url" target="_blank" class="text-indigo-600 hover:underline">Lihat bukti</a>
                </div>

                <form class="space-y-3" @submit.prevent="submitProof">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            {{ transaction.manual_transfer_proof_url ? 'Kirim ulang bukti transfer' : 'Unggah bukti transfer' }}
                        </label>
                        <input
                            type="file"
                            accept="image/*"
                            class="mt-1 block w-full text-sm"
                            @change="proofForm.proof = $event.target.files[0]"
                        />
                        <InputError class="mt-2" :message="proofForm.errors.proof" />
                    </div>
                    <PrimaryButton :disabled="proofForm.processing || !proofForm.proof">Kirim Bukti</PrimaryButton>
                </form>
            </section>

            <!-- Timeline status -->
            <section class="rounded-lg bg-white p-6 shadow-sm">
                <h3 class="mb-4 font-semibold text-gray-900">Status Pengerjaan</h3>
                <ol class="space-y-3">
                    <li v-for="(stage, i) in stages" :key="stage.value" class="flex items-center gap-3">
                        <span
                            class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-xs font-bold"
                            :class="i <= currentStageIndex ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-500'"
                        >
                            {{ i + 1 }}
                        </span>
                        <span :class="i <= currentStageIndex ? 'font-medium text-gray-900' : 'text-gray-400'">
                            {{ stage.label }}
                        </span>
                    </li>
                </ol>
            </section>

            <!-- Dokumentasi -->
            <section class="rounded-lg bg-white p-6 shadow-sm">
                <h3 class="mb-4 font-semibold text-gray-900">Dokumentasi</h3>
                <DocumentationGallery :documentations="transaction.documentations ?? []" />
            </section>
        </div>
    </AuthenticatedLayout>
</template>
