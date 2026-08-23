<script setup>
import DocumentationGallery from '@/Components/DocumentationGallery.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    transaction: {
        type: Object,
        required: true,
    },
    stages: {
        type: Array,
        required: true,
    },
    cloudinary_configured: {
        type: Boolean,
        default: false,
    },
    upload_signature: {
        type: Object,
        default: null,
    },
});

const page = usePage();

const currentIndex = computed(() =>
    props.stages.findIndex((s) => s.value === props.transaction.status),
);
const nextStage = computed(() => props.stages[currentIndex.value + 1] ?? null);

const advanceButtonLabel = computed(() =>
    props.transaction.status === 'dibayar' && nextStage.value?.value === 'hewan_disiapkan'
        ? 'Selesai, Lanjut ke Hewan Disiapkan'
        : `Naikkan Tahap: ${nextStage.value?.label ?? ''}`,
);

const rupiah = (v) =>
    new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(Number(v || 0));

const tanggal = (iso) =>
    new Date(iso).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });

// Naikkan status pengerjaan
const statusForm = useForm({ status: '' });
const advanceStatus = () => {
    if (!nextStage.value) return;
    if (!confirm(`Apakah Anda yakin ingin menaikkan status tahapan ke "${nextStage.value.label}"?`)) return;
    statusForm.status = nextStage.value.value;
    statusForm.post(`/admin/transaksi/${props.transaction.transaction_code}/status`, {
        preserveScroll: true,
    });
};

// Setujui Pembayaran
const approvePayment = () => {
    if (confirm(`Setujui bukti pembayaran untuk invoice ${props.transaction.transaction_code}?`)) {
        router.post(`/admin/transaksi/${props.transaction.transaction_code}/setujui`, {}, { preserveScroll: true });
    }
};

// Tolak Pembayaran
const isRejectOpen = ref(false);
const rejectForm = useForm({ reason: '' });
const submitReject = () => {
    rejectForm.post(`/admin/transaksi/${props.transaction.transaction_code}/tolak`, {
        preserveScroll: true,
        onSuccess: () => {
            isRejectOpen.value = false;
            rejectForm.reset();
        },
    });
};

// Hapus Transaksi
const isDeleteOpen = ref(false);
const deleteForm = useForm({});
const submitDelete = () => {
    deleteForm.delete(`/admin/transaksi/${props.transaction.transaction_code}`, {
        onSuccess: () => {
            isDeleteOpen.value = false;
        },
    });
};

// Upload dokumentasi
const docForm = useForm({
    stage: props.transaction.status,
    caption: '',
    file: null,
    file_url: null,
    cloudinary_public_id: null,
    type: null,
});
const docPreview = ref(null);
const uploading = ref(false);
const uploadError = ref('');

const handleDocFileChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        docForm.file = file;
        docPreview.value = URL.createObjectURL(file);
    }
};

const submitDocumentation = async () => {
    uploadError.value = '';

    if (props.cloudinary_configured && docForm.file) {
        uploading.value = true;
        try {
            const sig = props.upload_signature;
            const fd = new FormData();
            fd.append('file', docForm.file);
            fd.append('api_key', sig.api_key);
            fd.append('timestamp', sig.timestamp);
            fd.append('signature', sig.signature);
            fd.append('folder', sig.folder);

            const res = await fetch(
                `https://api.cloudinary.com/v1_1/${sig.cloud_name}/auto/upload`,
                { method: 'POST', body: fd }
            );
            if (!res.ok) throw new Error(`Cloudinary menolak upload (${res.status}).`);
            const data = await res.json();

            docForm.file_url = data.secure_url;
            docForm.cloudinary_public_id = data.public_id;
            docForm.type = data.resource_type === 'video' ? 'video' : 'photo';
            docForm.file = null;
        } catch (e) {
            uploadError.value = e.message;
            uploading.value = false;
            return;
        }
        uploading.value = false;
    }

    docForm.post(`/admin/transaksi/${props.transaction.transaction_code}/dokumentasi`, {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            docForm.reset('caption', 'file', 'file_url', 'cloudinary_public_id', 'type');
            docPreview.value = null;
        },
    });
};

const deleteDoc = (doc) => {
    if (confirm('Apakah Anda yakin ingin menghapus dokumentasi ini?')) {
        router.delete(`/admin/dokumentasi/${doc.id}`, { preserveScroll: true });
    }
};

const t = computed(() => props.transaction);
</script>

<template>
    <Head :title="`Detail Transaksi #${transaction.transaction_code} - Admin`" />

    <AdminLayout>
        <template #header>
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="flex items-center gap-2">
                        <Link :href="route('admin.transactions.index')" class="text-xs font-semibold text-gray-500 hover:text-brand-600">
                            &larr; Semua Transaksi
                        </Link>
                        <span class="text-xs text-gray-300">/</span>
                        <span class="font-mono text-xs font-bold text-gray-800">{{ transaction.transaction_code }}</span>
                    </div>
                    <h2 class="mt-1 font-mono text-xl font-bold tracking-tight text-gray-900">
                        {{ transaction.transaction_code }}
                    </h2>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <StatusBadge :status="t.payment_status" />
                    <StatusBadge :status="t.status" />
                    <button
                        type="button"
                        @click="isDeleteOpen = true"
                        class="ml-2 inline-flex items-center gap-1 rounded-xl bg-rose-50 px-3 py-1.5 text-xs font-bold text-rose-600 transition hover:bg-rose-600 hover:text-white"
                        title="Hapus Transaksi"
                    >
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Hapus Transaksi
                    </button>
                </div>
            </div>
        </template>

        <div class="space-y-6">
            <!-- Flash Notice -->
            <div
                v-if="page.props.flash?.success"
                class="flex items-center gap-3 rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-sm text-emerald-800 shadow-sm"
            >
                <svg class="h-5 w-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ page.props.flash.success }}</span>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-12 lg:items-start">
                
                <!-- LEFT COLUMN (7 Cols): Status Progress & Upload Dokumentasi -->
                <div class="space-y-6 lg:col-span-7">
                    
                    <!-- Stepper Tahapan Status Pengerjaan -->
                    <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                            <div>
                                <h3 class="text-base font-bold text-gray-900">Tahapan Pengerjaan Hewan</h3>
                                <p class="text-xs text-gray-500">Kelola kenaikan status tahapan qurban/aqiqah</p>
                            </div>
                            <span class="font-mono text-xs font-bold text-brand-600 bg-brand-50 px-2.5 py-1 rounded-lg">
                                Tahap {{ currentIndex + 1 }} dari {{ stages.length }}
                            </span>
                        </div>

                        <!-- Horizontal Stepper -->
                        <div class="mt-6 flex flex-wrap items-center gap-2">
                            <div
                                v-for="(stage, i) in stages"
                                :key="stage.value"
                                class="flex items-center gap-2"
                            >
                                <span
                                    class="flex h-7 w-7 items-center justify-center rounded-full text-xs font-bold shadow-sm"
                                    :class="i <= currentIndex ? 'bg-brand-500 text-white' : 'bg-gray-100 text-gray-400'"
                                >
                                    {{ i + 1 }}
                                </span>
                                <span
                                    class="text-xs font-semibold"
                                    :class="i <= currentIndex ? 'text-gray-900' : 'text-gray-400'"
                                >
                                    {{ stage.label }}
                                </span>
                                <span v-if="i < stages.length - 1" class="text-gray-300">→</span>
                            </div>
                        </div>

                        <!-- Action Advance Status Button -->
                        <div class="mt-6 border-t border-gray-100 pt-4 flex items-center justify-between">
                            <p v-if="statusForm.errors.status" class="text-xs text-rose-600">{{ statusForm.errors.status }}</p>
                            <p v-else-if="!nextStage" class="text-xs font-bold text-emerald-600">
                                Seluruh tahapan pengerjaan telah selesai.
                            </p>
                            <span v-else class="text-xs text-gray-500">
                                Klik tombol di samping untuk memajukan tahapan:
                            </span>

                            <button
                                v-if="nextStage"
                                type="button"
                                class="inline-flex items-center gap-1.5 rounded-xl bg-brand-500 px-4 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-brand-600 disabled:opacity-40"
                                :disabled="statusForm.processing"
                                @click="advanceStatus"
                            >
                                {{ advanceButtonLabel }} &rarr;
                            </button>
                        </div>
                    </div>

                    <!-- Upload & Kelola Dokumentasi -->
                    <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                        <div class="border-b border-gray-100 pb-4">
                            <h3 class="text-base font-bold text-gray-900">Dokumentasi Pelaksanaan</h3>
                            <p class="text-xs text-gray-500">Unggah foto / video bukti pelaksanaan syari untuk pembeli</p>
                        </div>

                        <!-- Form Upload Dokumentasi -->
                        <form class="mt-6 space-y-4 rounded-xl border border-gray-200/80 bg-gray-50/50 p-4" @submit.prevent="submitDocumentation">
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700">Tahap Terkait</label>
                                    <select v-model="docForm.stage" class="mt-1 block w-full rounded-xl border-gray-300 text-xs shadow-sm focus:border-brand-500 focus:ring-brand-500">
                                        <option v-for="s in stages" :key="s.value" :value="s.value">{{ s.label }}</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700">Keterangan / Caption (Opsional)</label>
                                    <input
                                        v-model="docForm.caption"
                                        type="text"
                                        placeholder="Contoh: Proses penyembelihan atas nama..."
                                        class="mt-1 block w-full rounded-xl border-gray-300 text-xs shadow-sm focus:border-brand-500 focus:ring-brand-500"
                                    />
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700">File Foto / Video</label>
                                <input
                                    type="file"
                                    accept="image/*,video/*"
                                    class="mt-1 block w-full text-xs text-gray-500 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-brand-700 hover:file:bg-brand-100"
                                    @change="handleDocFileChange"
                                />
                                <p class="mt-1 text-[11px] text-gray-400">
                                    {{ cloudinary_configured
                                        ? 'File otomatis diunggah ke Cloudinary Storage.'
                                        : 'File disimpan ke storage publik lokal (maks. 20MB).' }}
                                </p>
                                <p v-if="uploadError" class="mt-1 text-xs text-rose-600">{{ uploadError }}</p>
                                <p v-if="docForm.errors.file" class="mt-1 text-xs text-rose-600">{{ docForm.errors.file }}</p>
                            </div>

                            <div v-if="docPreview" class="h-28 w-28 overflow-hidden rounded-xl border border-gray-200">
                                <img :src="docPreview" alt="Preview Dokumen" class="h-full w-full object-cover" />
                            </div>

                            <div class="flex justify-end">
                                <button
                                    type="submit"
                                    class="inline-flex items-center gap-1.5 rounded-xl bg-brand-500 px-4 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-brand-600 disabled:opacity-40"
                                    :disabled="uploading || docForm.processing || !docForm.file"
                                >
                                    {{ uploading ? 'Mengunggah…' : 'Unggah File Dokumentasi' }}
                                </button>
                            </div>
                        </form>

                        <!-- Galeri Dokumentasi -->
                        <div class="mt-6">
                            <DocumentationGallery :documentations="t.documentations ?? []" />
                        </div>

                        <!-- Daftar Berkas & Tombol Hapus -->
                        <div v-if="t.documentations?.length" class="mt-6 border-t border-gray-100 pt-4">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-2">Daftar Berkas Terunggah</h4>
                            <div class="divide-y divide-gray-100">
                                <div
                                    v-for="doc in t.documentations"
                                    :key="doc.id"
                                    class="flex items-center justify-between py-2.5 text-xs"
                                >
                                    <div class="truncate text-gray-700">
                                        <span class="font-bold text-gray-900">[{{ doc.stage }}]</span>
                                        {{ doc.caption || 'Tanpa keterangan' }}
                                    </div>
                                    <button
                                        type="button"
                                        class="ml-3 text-xs font-bold text-rose-600 hover:text-rose-800 hover:underline"
                                        @click="deleteDoc(doc)"
                                    >
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN (5 Cols): Ringkasan Invoice & Moderasi Pembayaran -->
                <div class="space-y-6 lg:col-span-5">
                    
                    <!-- Moderasi Bukti Transfer Manual -->
                    <div
                        v-if="t.payment_method === 'manual_transfer'"
                        class="rounded-2xl border-2 border-brand-500/60 bg-white p-6 shadow-sm"
                    >
                        <h3 class="text-base font-bold text-gray-900">Bukti Transfer Manual</h3>
                        
                        <div v-if="t.manual_transfer_proof_url" class="mt-4 space-y-3">
                            <div class="overflow-hidden rounded-xl border border-gray-200 bg-gray-50">
                                <a :href="t.manual_transfer_proof_url" target="_blank" title="Klik untuk memperbesar">
                                    <img
                                        :src="t.manual_transfer_proof_url"
                                        alt="Bukti transfer"
                                        class="h-48 w-full object-contain"
                                    />
                                </a>
                            </div>

                            <div v-if="t.payment_status === 'pending'" class="grid grid-cols-2 gap-2 pt-2">
                                <button
                                    type="button"
                                    class="rounded-xl bg-emerald-600 px-3 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-emerald-700"
                                    @click="approvePayment"
                                >
                                    Setujui Pembayaran
                                </button>
                                <button
                                    type="button"
                                    class="rounded-xl bg-amber-600 px-3 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-amber-700"
                                    @click="isRejectOpen = true"
                                >
                                    Tolak Bukti
                                </button>
                            </div>
                        </div>
                        <p v-else class="mt-3 text-xs text-gray-500">
                            Pembeli belum mengunggah struk / bukti transfer.
                        </p>
                        <p v-if="t.rejected_reason" class="mt-3 text-xs text-rose-600">
                            Alasan ditolak: {{ t.rejected_reason }}
                        </p>
                    </div>

                    <!-- Ringkasan Invoice Detail -->
                    <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                        <h3 class="text-base font-bold text-gray-900 border-b border-gray-100 pb-3">Rincian Transaksi</h3>

                        <dl class="mt-4 space-y-2.5 text-xs text-gray-600">
                            <div class="flex justify-between">
                                <dt class="text-gray-400">Pembeli</dt>
                                <dd class="font-bold text-gray-900">{{ t.user?.name }} ({{ t.user?.email }})</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-400">Tanggal Transaksi</dt>
                                <dd class="font-medium text-gray-900">{{ tanggal(t.created_at) }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-400">Layanan</dt>
                                <dd class="font-medium text-gray-900">{{ t.service?.name }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-400">Produk Hewan</dt>
                                <dd class="font-medium text-gray-900">{{ t.product?.name }} &times; {{ t.quantity }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-400">Metode Pembayaran</dt>
                                <dd class="font-bold text-gray-900 capitalize">
                                    {{ t.payment_method === 'midtrans' ? 'Midtrans (Otomatis)' : 'Transfer Manual' }}
                                </dd>
                            </div>
                            <div v-if="t.approved_by" class="flex justify-between">
                                <dt class="text-gray-400">Divalidasi Oleh</dt>
                                <dd class="font-medium text-gray-900">{{ t.approved_by?.name ?? t.approvedBy?.name ?? '-' }}</dd>
                            </div>
                            <div class="flex justify-between border-t border-gray-100 pt-3 text-sm">
                                <dt class="font-bold text-gray-900">Total Pembayaran</dt>
                                <dd class="font-black text-brand-600">{{ rupiah(t.total_amount) }}</dd>
                            </div>
                        </dl>

                        <!-- Rincian Alamat Penyaluran -->
                        <div class="mt-5 rounded-xl bg-gray-50 p-3.5 text-xs">
                            <span class="font-bold uppercase tracking-wider text-gray-500 block mb-1">
                                Penyaluran:
                            </span>
                            <div v-if="t.distribution_type === 'pt_yayasan'">
                                <p class="font-semibold text-gray-800">Disalurkan Yayasan & Dhuafa</p>
                                <p class="text-gray-500 mt-0.5">Daging disalurkan kepada kaum dhuafa pelosok.</p>
                            </div>
                            <div v-else class="text-gray-700 space-y-0.5">
                                <p class="font-bold">{{ t.recipient_name }} ({{ t.recipient_phone }})</p>
                                <p class="text-gray-500 text-[11px]">
                                    {{ t.recipient_address }}, {{ t.recipient_district }}, {{ t.recipient_city }}, {{ t.recipient_province }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Tolak Pembayaran -->
        <div
            v-if="isRejectOpen"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm"
            @click.self="isRejectOpen = false"
        >
            <form class="w-full max-w-md space-y-4 rounded-2xl bg-white p-6 shadow-2xl" @submit.prevent="submitReject">
                <h3 class="text-base font-bold text-gray-900">
                    Tolak Bukti Pembayaran
                </h3>
                <p class="font-mono text-xs text-gray-500">Invoice: {{ transaction.transaction_code }}</p>

                <div>
                    <label class="block text-xs font-bold text-gray-700">Alasan Penolakan</label>
                    <textarea
                        v-model="rejectForm.reason"
                        rows="3"
                        placeholder="Contoh: Bukti buram, nominal transfer tidak cocok..."
                        class="mt-1 block w-full rounded-xl border-gray-300 text-xs shadow-sm focus:border-brand-500 focus:ring-brand-500"
                        required
                    ></textarea>
                    <p v-if="rejectForm.errors.reason" class="mt-1 text-xs text-rose-600">{{ rejectForm.errors.reason }}</p>
                </div>

                <div class="flex justify-end gap-2 border-t border-gray-100 pt-3">
                    <button
                        type="button"
                        class="rounded-xl border border-gray-300 bg-white px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50"
                        @click="isRejectOpen = false"
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

        <!-- Modal Hapus Transaksi -->
        <div
            v-if="isDeleteOpen"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm"
            @click.self="isDeleteOpen = false"
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
                        <p class="font-mono text-xs text-gray-500">Invoice: {{ transaction.transaction_code }}</p>
                    </div>
                </div>

                <p class="text-xs text-gray-600 leading-relaxed">
                    Apakah Anda yakin ingin menghapus data transaksi ini secara permanen? Stok hewan akan dikembalikan secara otomatis jika pesanan belum dibatalkan.
                </p>

                <div class="flex justify-end gap-2 border-t border-gray-100 pt-3">
                    <button
                        type="button"
                        class="rounded-xl border border-gray-300 bg-white px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50"
                        @click="isDeleteOpen = false"
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
