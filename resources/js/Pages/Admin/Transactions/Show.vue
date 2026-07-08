<script setup>
import DocumentationGallery from '@/Components/DocumentationGallery.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    transaction: Object,
    stages: Array,
    cloudinary_configured: Boolean,
    upload_signature: Object,
});

const page = usePage();

const currentIndex = computed(() =>
    props.stages.findIndex((s) => s.value === props.transaction.status),
);
const nextStage = computed(() => props.stages[currentIndex.value + 1] ?? null);

// Dibayar → Hewan Disiapkan tidak butuh bukti dokumentasi, cukup konfirmasi selesai.
const advanceButtonLabel = computed(() =>
    props.transaction.status === 'dibayar' && nextStage.value?.value === 'hewan_disiapkan'
        ? 'Selesai, Lanjut ke Hewan Disiapkan'
        : `Naikkan ke: ${nextStage.value?.label ?? ''}`,
);

const rupiah = (v) =>
    new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(v);

const tanggal = (iso) =>
    new Date(iso).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' });

// --- Naikkan status ---
const statusForm = useForm({ status: '' });
const advanceStatus = () => {
    if (!nextStage.value) return;
    if (!confirm(`Naikkan status ke "${nextStage.value.label}"?`)) return;
    statusForm.status = nextStage.value.value;
    statusForm.post(`/admin/transaksi/${props.transaction.transaction_code}/status`, {
        preserveScroll: true,
    });
};

// --- Upload dokumentasi ---
const docForm = useForm({
    stage: props.transaction.status,
    caption: '',
    file: null,
    file_url: null,
    cloudinary_public_id: null,
    type: null,
});
const uploading = ref(false);
const uploadError = ref('');

const submitDocumentation = async () => {
    uploadError.value = '';

    if (props.cloudinary_configured && docForm.file) {
        // Direct-to-Cloudinary signed upload; server hanya terima URL hasilnya.
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
                { method: 'POST', body: fd },
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
        onSuccess: () => docForm.reset('caption', 'file', 'file_url', 'cloudinary_public_id', 'type'),
    });
};

const deleteDoc = (doc) => {
    if (confirm('Hapus dokumentasi ini?')) {
        router.delete(`/admin/dokumentasi/${doc.id}`, { preserveScroll: true });
    }
};

const t = computed(() => props.transaction);
</script>

<template>
    <Head :title="`Transaksi ${transaction.transaction_code}`" />

    <AdminLayout>
        <template #header>
            <div class="flex flex-wrap items-center justify-between gap-2">
                <h2 class="font-mono text-xl font-semibold leading-tight text-gray-800">
                    {{ transaction.transaction_code }}
                </h2>
                <div class="flex gap-2">
                    <StatusBadge :status="t.payment_status" />
                    <StatusBadge :status="t.status" />
                </div>
            </div>
        </template>

        <div class="space-y-6">
            <div
                v-if="page.props.flash?.success"
                class="rounded-lg bg-green-50 p-4 text-sm text-green-700"
            >
                {{ page.props.flash.success }}
            </div>

            <!-- Ringkasan -->
            <section class="rounded-lg bg-white p-6 shadow-sm">
                <h3 class="mb-4 font-semibold text-gray-900">Ringkasan</h3>
                <dl class="grid gap-x-8 gap-y-2 text-sm sm:grid-cols-2">
                    <div class="flex justify-between"><dt class="text-gray-500">Pembeli</dt><dd>{{ t.user?.name }} ({{ t.user?.email }})</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Tanggal</dt><dd>{{ tanggal(t.created_at) }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Layanan</dt><dd>{{ t.service?.name }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Produk</dt><dd>{{ t.product?.name }} × {{ t.quantity }}</dd></div>
                    <div class="flex justify-between font-semibold"><dt class="text-gray-500">Total</dt><dd>{{ rupiah(t.total_amount) }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Metode</dt><dd>{{ t.payment_method === 'midtrans' ? 'Midtrans' : 'Transfer Manual' }}</dd></div>
                    <div v-if="t.approved_by" class="flex justify-between">
                        <dt class="text-gray-500">Divalidasi oleh</dt>
                        <dd>{{ t.approved_by?.name ?? t.approvedBy?.name ?? '-' }}</dd>
                    </div>
                </dl>

                <div class="mt-4 border-t pt-4 text-sm">
                    <p class="font-medium text-gray-700">Penyaluran</p>
                    <p v-if="t.distribution_type === 'pt_yayasan'" class="text-gray-600">PT/Yayasan.</p>
                    <div v-else class="text-gray-600">
                        <p>{{ t.recipient_name }} · {{ t.recipient_phone }}</p>
                        <p>{{ t.recipient_address }}, {{ t.recipient_district }}, {{ t.recipient_city }}, {{ t.recipient_province }}</p>
                    </div>
                </div>
            </section>

            <!-- Bukti transfer -->
            <section
                v-if="t.payment_method === 'manual_transfer'"
                class="rounded-lg bg-white p-6 shadow-sm"
            >
                <h3 class="mb-4 font-semibold text-gray-900">Bukti Transfer</h3>
                <div v-if="t.manual_transfer_proof_url">
                    <a :href="t.manual_transfer_proof_url" target="_blank">
                        <img
                            :src="t.manual_transfer_proof_url"
                            alt="Bukti transfer"
                            class="max-h-64 rounded border"
                        />
                    </a>
                    <div v-if="t.payment_status === 'pending'" class="mt-4 flex gap-2">
                        <button
                            class="rounded bg-green-600 px-3 py-2 text-sm text-white hover:bg-green-700"
                            @click="router.post(`/admin/transaksi/${t.transaction_code}/setujui`, {}, { preserveScroll: true })"
                        >
                            Setujui Pembayaran
                        </button>
                        <span class="self-center text-xs text-gray-500">Penolakan dilakukan dari daftar transaksi.</span>
                    </div>
                </div>
                <p v-else class="text-sm text-gray-500">Pembeli belum mengunggah bukti transfer.</p>
                <p v-if="t.rejected_reason" class="mt-2 text-sm text-red-600">Ditolak: {{ t.rejected_reason }}</p>
            </section>

            <!-- Stepper status -->
            <section class="rounded-lg bg-white p-6 shadow-sm">
                <h3 class="mb-4 font-semibold text-gray-900">Tahapan Status</h3>
                <ol class="mb-4 flex flex-wrap items-center gap-2">
                    <li v-for="(stage, i) in stages" :key="stage.value" class="flex items-center gap-2">
                        <span
                            class="flex h-7 w-7 items-center justify-center rounded-full text-xs font-bold"
                            :class="i <= currentIndex ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-500'"
                        >
                            {{ i + 1 }}
                        </span>
                        <span class="text-sm" :class="i <= currentIndex ? 'font-medium text-gray-900' : 'text-gray-400'">
                            {{ stage.label }}
                        </span>
                        <span v-if="i < stages.length - 1" class="text-gray-300">→</span>
                    </li>
                </ol>
                <p v-if="statusForm.errors.status" class="mb-2 text-sm text-red-600">{{ statusForm.errors.status }}</p>
                <button
                    v-if="nextStage"
                    class="rounded bg-brand-600 px-3 py-2 text-sm text-white hover:bg-brand-700 disabled:opacity-40"
                    :disabled="statusForm.processing"
                    @click="advanceStatus"
                >
                    {{ advanceButtonLabel }}
                </button>
                <p v-else class="text-sm text-green-700">Semua tahapan selesai.</p>
            </section>

            <!-- Dokumentasi -->
            <section class="rounded-lg bg-white p-6 shadow-sm">
                <h3 class="mb-4 font-semibold text-gray-900">Dokumentasi</h3>

                <form class="mb-6 grid gap-3 rounded border border-gray-200 p-4 sm:grid-cols-2" @submit.prevent="submitDocumentation">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tahap</label>
                        <select v-model="docForm.stage" class="mt-1 block w-full rounded-md border-gray-300 text-sm">
                            <option v-for="s in stages" :key="s.value" :value="s.value">{{ s.label }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Keterangan (opsional)</label>
                        <input
                            v-model="docForm.caption"
                            type="text"
                            class="mt-1 block w-full rounded-md border-gray-300 text-sm"
                        />
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">File foto/video</label>
                        <input
                            type="file"
                            accept="image/*,video/*"
                            class="mt-1 block w-full text-sm"
                            @change="docForm.file = $event.target.files[0]"
                        />
                        <p class="mt-1 text-xs text-gray-500">
                            {{ cloudinary_configured
                                ? 'File dikirim langsung ke Cloudinary (signed upload).'
                                : 'Cloudinary belum dikonfigurasi — file diunggah lewat server (maks 20MB).' }}
                        </p>
                        <p v-if="uploadError" class="mt-1 text-sm text-red-600">{{ uploadError }}</p>
                        <p v-if="docForm.errors.file" class="mt-1 text-sm text-red-600">{{ docForm.errors.file }}</p>
                        <p v-if="docForm.errors.file_url" class="mt-1 text-sm text-red-600">{{ docForm.errors.file_url }}</p>
                    </div>
                    <div class="sm:col-span-2">
                        <button
                            type="submit"
                            class="rounded bg-brand-600 px-3 py-2 text-sm text-white hover:bg-brand-700 disabled:opacity-40"
                            :disabled="uploading || docForm.processing || !docForm.file"
                        >
                            {{ uploading ? 'Mengunggah…' : 'Unggah Dokumentasi' }}
                        </button>
                    </div>
                </form>

                <DocumentationGallery :documentations="t.documentations ?? []" />

                <ul v-if="t.documentations?.length" class="mt-4 space-y-1">
                    <li
                        v-for="doc in t.documentations"
                        :key="doc.id"
                        class="flex items-center justify-between rounded border border-gray-100 px-3 py-2 text-sm"
                    >
                        <span class="truncate text-gray-600">
                            [{{ doc.stage }}] {{ doc.caption || doc.file_url }}
                            <span class="text-gray-400">— {{ doc.uploaded_by?.name ?? doc.uploadedBy?.name ?? '' }}</span>
                        </span>
                        <button class="ml-3 text-red-600 hover:underline" @click="deleteDoc(doc)">Hapus</button>
                    </li>
                </ul>
            </section>
        </div>
    </AdminLayout>
</template>
