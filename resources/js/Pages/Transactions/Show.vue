<script setup>
import DocumentationGallery from '@/Components/DocumentationGallery.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';

const props = defineProps({
    transaction: {
        type: Object,
        required: true,
    },
    bank_accounts: {
        type: Array,
        default: () => [],
    },
    midtrans_client_key: {
        type: String,
        default: null,
    },
    flash: {
        type: Object,
        default: () => ({}),
    },
});

const stages = [
    { value: 'menunggu', label: 'Menunggu', desc: 'Pesanan dibuat & menunggu konfirmasi pembayaran' },
    { value: 'dibayar', label: 'Terbayar', desc: 'Pembayaran telah terverifikasi lunas' },
    { value: 'hewan_disiapkan', label: 'Hewan Disiapkan', desc: 'Hewan dikarantina, dicek kesehatan, & diberi tanda qurban' },
    { value: 'tersembelih', label: 'Penyembelihan', desc: 'Penyembelihan syari di RPH & dokumentasi foto/video diambil' },
    { value: 'didistribusikan', label: 'Pendistribusian & Selesai', desc: 'Daging dicacah, dikemas, & disalurkan ke penerima manfaat' },
];

const currentStageIndex = computed(() =>
    stages.findIndex((s) => s.value === props.transaction.status),
);

const rupiah = (v) =>
    new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(Number(v || 0));

const tanggal = (iso) =>
    new Date(iso).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });

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
const proofPreview = ref(null);
const proofForm = useForm({ proof: null });

const handleProofChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        proofForm.proof = file;
        proofPreview.value = URL.createObjectURL(file);
    }
};

const submitProof = () =>
    proofForm.post(`/pembayaran/${props.transaction.transaction_code}/bukti-transfer`, {
        forceFormData: true,
        onSuccess: () => {
            proofForm.reset();
            proofPreview.value = null;
        },
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
    try {
        const res = await window.axios.post(
            `/pembayaran/${props.transaction.transaction_code}/snap-token`,
        );
        const token = res.data.snap_token;
        if (window.snap && token) {
            window.snap.pay(token, {
                onSuccess: () => window.location.reload(),
                onPending: () => window.location.reload(),
                onError: () => window.location.reload(),
                onClose: () => {},
            });
        }
    } catch (e) {
        console.error(e);
    }
};

const failLabel = {
    rejected: 'Pembayaran Ditolak Admin',
    expired: 'Pembayaran Kedaluwarsa',
    cancelled: 'Transaksi Dibatalkan',
};
</script>

<template>
    <Head :title="`Detail Transaksi #${transaction.transaction_code}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="flex items-center gap-2">
                        <Link href="/transaksi" class="text-xs font-semibold text-gray-500 hover:text-brand-600">
                            &larr; Riwayat Transaksi
                        </Link>
                        <span class="text-xs text-gray-300">/</span>
                        <span class="font-mono text-xs font-bold text-gray-800">{{ transaction.transaction_code }}</span>
                    </div>
                    <h2 class="mt-1 font-mono text-xl font-bold tracking-tight text-gray-900">
                        {{ transaction.transaction_code }}
                    </h2>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <StatusBadge :status="transaction.payment_status" />
                    <StatusBadge :status="transaction.status" />
                </div>
            </div>
        </template>

        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <!-- Flash Notice -->
            <div
                v-if="flash?.success"
                class="mb-6 flex items-center gap-3 rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-sm text-emerald-800 shadow-sm"
            >
                <svg class="h-5 w-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ flash.success }}</span>
            </div>

            <!-- Banner Gagal / Batal -->
            <div v-if="isFailed" class="mb-6 rounded-2xl bg-rose-50 border border-rose-200 p-5 shadow-sm">
                <div class="flex items-start gap-3">
                    <svg class="h-5 w-5 text-rose-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <h4 class="font-bold text-rose-800">{{ failLabel[transaction.payment_status] }}</h4>
                        <p v-if="transaction.rejected_reason" class="mt-1 text-xs text-rose-700">
                            Catatan: {{ transaction.rejected_reason }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-8 lg:grid-cols-12 lg:items-start">
                
                <!-- LEFT COLUMN (7 Cols): Status Timeline & Dokumentasi Pelaksanaan -->
                <div class="space-y-6 lg:col-span-7">
                    
                    <!-- 1. Interactive Step Timeline Status Pelaksanaan -->
                    <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                        <div class="border-b border-gray-100 pb-4">
                            <h3 class="text-base font-bold text-gray-900">Pelacakan Tahapan Ibadah</h3>
                            <p class="text-xs text-gray-500">Proses pelaksanaan syari hewan Anda secara transparan</p>
                        </div>

                        <div class="mt-6 flow-root">
                            <ul class="-mb-8">
                                <li v-for="(stage, idx) in stages" :key="stage.value">
                                    <div class="relative pb-8">
                                        <span
                                            v-if="idx !== stages.length - 1"
                                            class="absolute left-4 top-4 -ml-px h-full w-0.5"
                                            :class="idx < currentStageIndex ? 'bg-brand-500' : 'bg-gray-200'"
                                            aria-hidden="true"
                                        ></span>
                                        <div class="relative flex items-start space-x-3">
                                            <div>
                                                <span
                                                    class="flex h-8 w-8 items-center justify-center rounded-full text-xs font-bold ring-4 ring-white"
                                                    :class="
                                                        idx <= currentStageIndex
                                                            ? 'bg-brand-500 text-white'
                                                            : 'bg-gray-100 text-gray-400'
                                                    "
                                                >
                                                    <svg v-if="idx < currentStageIndex" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    <span v-else>{{ idx + 1 }}</span>
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1 pt-1.5">
                                                <div class="flex items-center justify-between">
                                                    <p
                                                        class="text-sm font-bold"
                                                        :class="idx <= currentStageIndex ? 'text-gray-900' : 'text-gray-400'"
                                                    >
                                                        {{ stage.label }}
                                                    </p>
                                                    <span
                                                        v-if="idx === currentStageIndex"
                                                        class="rounded-full bg-brand-50 px-2 py-0.5 text-[10px] font-bold text-brand-700 animate-pulse"
                                                    >
                                                        Sedang Berjalan
                                                    </span>
                                                </div>
                                                <p class="mt-0.5 text-xs text-gray-500">{{ stage.desc }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- 2. Galeri Dokumentasi Foto & Video Pelaksanaan -->
                    <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                            <div>
                                <h3 class="text-base font-bold text-gray-900">Dokumentasi Pelaksanaan</h3>
                                <p class="text-xs text-gray-500">Bukti foto & video pemotongan hingga distribusi</p>
                            </div>
                            <span class="rounded-full bg-zinc-100 px-2.5 py-1 text-xs font-semibold text-zinc-700">
                                {{ transaction.documentations?.length || 0 }} Berkas
                            </span>
                        </div>

                        <div class="mt-6">
                            <DocumentationGallery :documentations="transaction.documentations ?? []" />
                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN (5 Cols): Invoice Rincian + Aksi Pembayaran -->
                <div class="space-y-6 lg:col-span-5">
                    
                    <!-- Box Aksi Pembayaran Online Midtrans -->
                    <div v-if="isPendingMidtrans" class="rounded-2xl border-2 border-brand-500/80 bg-white p-6 shadow-lg">
                        <div class="flex items-center gap-2">
                            <span class="h-2.5 w-2.5 rounded-full bg-amber-500 animate-ping"></span>
                            <h3 class="text-base font-bold text-gray-900">Menunggu Pembayaran</h3>
                        </div>
                        <p class="mt-1 text-xs text-gray-500 leading-relaxed">
                            Selesaikan pembayaran tagihan Anda melalui Virtual Account, QRIS, GoPay, atau gerai retail.
                        </p>

                        <div class="mt-5 rounded-xl bg-brand-50 p-4 border border-brand-100">
                            <span class="text-xs text-gray-500">Total yang harus dibayar:</span>
                            <p class="text-2xl font-black text-brand-600 mt-0.5">
                                {{ rupiah(transaction.total_amount) }}
                            </p>
                        </div>

                        <div class="mt-5">
                            <PrimaryButton
                                class="w-full justify-center !rounded-xl !py-3.5 !text-sm font-bold shadow-md"
                                :disabled="paying.processing"
                                @click="payNow"
                            >
                                {{ paying.processing ? 'Menghubungkan...' : 'Bayar Sekarang via Midtrans' }}
                            </PrimaryButton>
                        </div>
                    </div>

                    <!-- Box Aksi Transfer Manual & Upload Bukti -->
                    <div v-if="isPendingManual" class="rounded-2xl border-2 border-brand-500/80 bg-white p-6 shadow-lg">
                        <div class="flex items-center gap-2">
                            <span class="h-2.5 w-2.5 rounded-full bg-amber-500 animate-ping"></span>
                            <h3 class="text-base font-bold text-gray-900">Instruksi Transfer Manual</h3>
                        </div>
                        <p class="mt-1 text-xs text-gray-500 leading-relaxed">
                            Silakan transfer tepat sebesar nominal di bawah ke salah satu rekening resmi:
                        </p>

                        <div class="mt-4 rounded-xl bg-brand-50 p-4 border border-brand-100">
                            <span class="text-xs text-gray-500">Total Transfer:</span>
                            <p class="text-2xl font-black text-brand-600 mt-0.5">
                                {{ rupiah(transaction.total_amount) }}
                            </p>
                        </div>

                        <!-- Daftar Rekening Bank -->
                        <div class="mt-4 space-y-2">
                            <div
                                v-for="acc in bank_accounts"
                                :key="acc.account_number"
                                class="rounded-xl border border-gray-200 bg-gray-50 p-3.5 text-xs"
                            >
                                <div class="flex items-center justify-between font-bold text-gray-900">
                                    <span>Bank {{ acc.bank }}</span>
                                    <span class="font-mono text-brand-700 text-sm bg-white px-2 py-0.5 rounded border border-gray-200">
                                        {{ acc.account_number }}
                                    </span>
                                </div>
                                <p class="mt-1 text-gray-500 text-[11px]">a.n. {{ acc.account_name }}</p>
                            </div>
                        </div>

                        <!-- Status Bukti Terkirim -->
                        <div
                            v-if="transaction.manual_transfer_proof_url"
                            class="mt-5 rounded-xl bg-emerald-50 border border-emerald-200 p-3.5 text-xs text-emerald-800 flex items-center justify-between"
                        >
                            <span>Bukti transfer telah dikirim & menunggu review admin.</span>
                            <a
                                :href="transaction.manual_transfer_proof_url"
                                target="_blank"
                                class="font-bold underline ml-2 shrink-0"
                            >
                                Buka Foto
                            </a>
                        </div>

                        <!-- Form Unggah Bukti -->
                        <form class="mt-5 border-t border-gray-100 pt-4 space-y-3" @submit.prevent="submitProof">
                            <div>
                                <label class="block text-xs font-bold text-gray-700">
                                    {{ transaction.manual_transfer_proof_url ? 'Upload Ulang Bukti Transfer' : 'Unggah Struk / Bukti Transfer' }}
                                </label>
                                <input
                                    type="file"
                                    accept="image/*"
                                    class="mt-1 block w-full text-xs text-gray-500 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-brand-700 hover:file:bg-brand-100"
                                    @change="handleProofChange"
                                    required
                                />
                                <InputError class="mt-1" :message="proofForm.errors.proof" />
                            </div>

                            <div v-if="proofPreview" class="h-28 w-28 overflow-hidden rounded-lg border border-gray-200">
                                <img :src="proofPreview" alt="Preview Bukti" class="h-full w-full object-cover" />
                            </div>

                            <PrimaryButton
                                class="w-full justify-center !rounded-xl !py-2.5 !text-xs font-bold shadow-sm"
                                :disabled="proofForm.processing || !proofForm.proof"
                            >
                                {{ proofForm.processing ? 'Mengirim...' : 'Kirim Bukti Pembayaran' }}
                            </PrimaryButton>
                        </form>
                    </div>

                    <!-- Kartu Rincian Invoice & Penyaluran -->
                    <div class="rounded-2xl border border-gray-200/80 bg-white p-6 shadow-sm">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                            <h3 class="text-base font-bold text-gray-900">Rincian Invoice</h3>
                            <span class="text-xs text-gray-400">{{ tanggal(transaction.created_at) }}</span>
                        </div>

                        <!-- Rincian Produk Hewan -->
                        <div class="mt-4 flex gap-3.5 border-b border-gray-100 pb-4">
                            <img
                                v-if="transaction.product?.primary_image_url"
                                :src="transaction.product.primary_image_url"
                                :alt="transaction.product.name"
                                class="h-14 w-14 shrink-0 rounded-xl object-cover border border-gray-100"
                            />
                            <div class="flex-1">
                                <h4 class="text-sm font-bold text-gray-900">{{ transaction.product?.name }}</h4>
                                <p class="text-xs text-gray-500">
                                    Layanan: {{ transaction.service?.name }} · {{ transaction.quantity }} ekor
                                </p>
                                <span v-if="transaction.product?.weight_estimate_kg" class="text-[11px] text-gray-400">
                                    Bobot: ~{{ transaction.product.weight_estimate_kg }} kg
                                </span>
                            </div>
                        </div>

                        <!-- Detail Biaya -->
                        <dl class="mt-4 space-y-2.5 text-xs text-gray-600">
                            <div class="flex justify-between">
                                <dt>Harga Satuan</dt>
                                <dd class="font-medium text-gray-900">{{ rupiah(transaction.unit_price) }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt>Jumlah Pesanan</dt>
                                <dd class="font-medium text-gray-900">{{ transaction.quantity }} Ekor</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt>Metode Bayar</dt>
                                <dd class="font-semibold capitalize text-gray-800">
                                    {{ transaction.payment_method === 'midtrans' ? 'Otomatis (Midtrans)' : 'Transfer Manual' }}
                                </dd>
                            </div>
                            <div class="flex justify-between border-t border-gray-100 pt-3 text-sm">
                                <dt class="font-bold text-gray-900">Total Pembayaran</dt>
                                <dd class="font-black text-brand-600">{{ rupiah(transaction.total_amount) }}</dd>
                            </div>
                        </dl>

                        <!-- Informasi Alamat / Penyaluran -->
                        <div class="mt-5 rounded-xl bg-gray-50 p-4 text-xs">
                            <span class="font-bold uppercase tracking-wider text-gray-500 block mb-1">
                                Penyaluran Daging:
                            </span>
                            <div v-if="transaction.distribution_type === 'pt_yayasan'">
                                <p class="font-semibold text-gray-800">Disalurkan Yayasan & Dhuafa</p>
                                <p class="text-gray-500 mt-0.5">
                                    Daging disalurkan kepada penerima manfaat dan santri terdaftar.
                                </p>
                            </div>
                            <div v-else class="text-gray-700 space-y-0.5">
                                <p class="font-bold">{{ transaction.recipient_name }} ({{ transaction.recipient_phone }})</p>
                                <p class="text-gray-500">
                                    {{ transaction.recipient_address }}, {{ transaction.recipient_district }}, {{ transaction.recipient_city }}, {{ transaction.recipient_province }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
