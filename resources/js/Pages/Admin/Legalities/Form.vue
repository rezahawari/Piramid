<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    legality: {
        type: Object,
        default: null,
    },
});

const isEditing = computed(() => !!props.legality);

const form = useForm({
    title: props.legality?.title ?? '',
    document_number: props.legality?.document_number ?? '',
    issuer: props.legality?.issuer ?? '',
    issued_date: props.legality?.issued_date ?? '',
    document_file: null,
    file_path: props.legality?.file_path ?? '',
    description: props.legality?.description ?? '',
    order: props.legality?.order ?? 1,
    is_active: props.legality ? Boolean(props.legality.is_active) : true,
});

const previewUrl = ref(props.legality?.file_path ?? null);
const selectedFileName = ref(null);
const detectedFileType = ref(props.legality?.file_type ?? 'pdf');

const handleFileChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.document_file = file;
        selectedFileName.value = file.name;
        const ext = file.name.split('.').pop().toLowerCase();
        detectedFileType.value = ['png', 'jpg', 'jpeg'].includes(ext) ? 'image' : 'pdf';

        if (['png', 'jpg', 'jpeg'].includes(ext)) {
            previewUrl.value = URL.createObjectURL(file);
        } else {
            previewUrl.value = null;
        }
    }
};

const submit = () => {
    if (isEditing.value) {
        form.transform((data) => ({
            ...data,
            _method: 'PUT',
        })).post(route('admin.legalities.update', props.legality.id), {
            forceFormData: true,
        });
    } else {
        form.post(route('admin.legalities.store'), {
            forceFormData: true,
        });
    }
};
</script>

<template>
    <Head :title="isEditing ? 'Edit Dokumen Legalitas - Admin' : 'Tambah Dokumen Legalitas - Admin'" />

    <AdminLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold leading-tight text-gray-900">
                        {{ isEditing ? 'Edit Dokumen Legalitas' : 'Tambah Dokumen Legalitas Baru' }}
                    </h2>
                    <p class="text-xs text-gray-500">
                        Unggah berkas legalitas dalam format PDF, PNG, atau JPG untuk ditampilkan di halaman beranda.
                    </p>
                </div>
                <Link
                    :href="route('admin.legalities.index')"
                    class="rounded-xl border border-gray-200 bg-white px-4 py-2 text-xs font-bold text-gray-700 shadow-2xs hover:bg-gray-50 transition"
                >
                    &larr; Kembali
                </Link>
            </div>
        </template>

        <div class="mx-auto max-w-4xl">
            <form @submit.prevent="submit" class="space-y-6">
                <!-- Main Card -->
                <div class="rounded-3xl border border-gray-200/90 bg-white p-6 sm:p-8 shadow-xs space-y-6">
                    <!-- Title & Issuer -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                                Nama / Judul Dokumen <span class="text-rose-500">*</span>
                            </label>
                            <input
                                v-model="form.title"
                                type="text"
                                placeholder="Contoh: Sertifikat Halal Rumah Potong Hewan"
                                class="w-full rounded-2xl border-gray-200 text-sm focus:border-brand-500 focus:ring-brand-500 shadow-2xs"
                                required
                            />
                            <p v-if="form.errors.title" class="mt-1 text-xs text-rose-500">{{ form.errors.title }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                                Penerbit / Lembaga Yang Mengeluarkan
                            </label>
                            <input
                                v-model="form.issuer"
                                type="text"
                                placeholder="Contoh: BPJPH Kemenag RI / MUI"
                                class="w-full rounded-2xl border-gray-200 text-sm focus:border-brand-500 focus:ring-brand-500 shadow-2xs"
                            />
                            <p v-if="form.errors.issuer" class="mt-1 text-xs text-rose-500">{{ form.errors.issuer }}</p>
                        </div>
                    </div>

                    <!-- Doc Number & Issued Date -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                                Nomor Dokumen / Sertifikat / SK
                            </label>
                            <input
                                v-model="form.document_number"
                                type="text"
                                placeholder="Contoh: ID32110001234560723"
                                class="w-full rounded-2xl border-gray-200 text-sm focus:border-brand-500 focus:ring-brand-500 shadow-2xs font-mono"
                            />
                            <p v-if="form.errors.document_number" class="mt-1 text-xs text-rose-500">{{ form.errors.document_number }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                                Tanggal Penerbitan / Tanggal Berlaku
                            </label>
                            <input
                                v-model="form.issued_date"
                                type="date"
                                class="w-full rounded-2xl border-gray-200 text-sm focus:border-brand-500 focus:ring-brand-500 shadow-2xs"
                            />
                            <p v-if="form.errors.issued_date" class="mt-1 text-xs text-rose-500">{{ form.errors.issued_date }}</p>
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                            Keterangan / Deskripsi Singkat
                        </label>
                        <textarea
                            v-model="form.description"
                            rows="3"
                            placeholder="Ringkasan atau penjelasan mengenai cakupan izin dan legalitas ibadah..."
                            class="w-full rounded-2xl border-gray-200 text-sm focus:border-brand-500 focus:ring-brand-500 shadow-2xs"
                        ></textarea>
                        <p v-if="form.errors.description" class="mt-1 text-xs text-rose-500">{{ form.errors.description }}</p>
                    </div>

                    <!-- Upload File Section (PDF, PNG, JPG) -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                            Berkas Dokumen (PDF, PNG, JPG) <span v-if="!isEditing" class="text-rose-500">*</span>
                        </label>

                        <div class="mt-1 flex justify-center rounded-3xl border-2 border-dashed border-gray-300 px-6 pt-5 pb-6 hover:border-brand-500 transition bg-gray-50/50">
                            <div class="space-y-2 text-center">
                                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-50 text-brand-600">
                                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                </div>
                                <div class="flex text-sm text-gray-600 justify-center">
                                    <label class="relative cursor-pointer rounded-md font-bold text-brand-600 focus-within:outline-none hover:text-brand-500">
                                        <span>Pilih berkas dokumen</span>
                                        <input
                                            type="file"
                                            class="sr-only"
                                            accept=".pdf,.png,.jpg,.jpeg,application/pdf,image/png,image/jpeg"
                                            @change="handleFileChange"
                                        />
                                    </label>
                                    <p class="pl-1">atau seret ke sini</p>
                                </div>
                                <p class="text-xs text-gray-500">
                                    Mendukung format <strong>PDF, PNG, JPG, JPEG</strong> (Maksimal 15 MB)
                                </p>
                                <div v-if="selectedFileName" class="mt-2 inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700 border border-emerald-200">
                                    <span>📄 {{ selectedFileName }}</span>
                                </div>
                            </div>
                        </div>
                        <p v-if="form.errors.document_file" class="mt-1 text-xs text-rose-500">{{ form.errors.document_file }}</p>

                        <!-- Existing / Image Preview -->
                        <div v-if="previewUrl" class="mt-4 rounded-2xl border border-gray-200 p-3 bg-white">
                            <span class="text-xs font-semibold text-gray-500 block mb-2">Pratinjau Gambar:</span>
                            <img :src="previewUrl" alt="Pratinjau" class="max-h-48 rounded-xl object-contain mx-auto" />
                        </div>
                        <div v-else-if="form.file_path" class="mt-4 flex items-center justify-between rounded-2xl border border-gray-200 p-3 bg-gray-50">
                            <div class="flex items-center gap-2">
                                <span class="rounded-lg bg-rose-500 px-2 py-0.5 text-[10px] font-bold text-white uppercase">
                                    {{ detectedFileType.toUpperCase() }}
                                </span>
                                <span class="text-xs text-gray-700 truncate max-w-sm">{{ form.file_path }}</span>
                            </div>
                            <a :href="form.file_path" target="_blank" class="text-xs font-bold text-brand-600 hover:underline">
                                Buka Berkas Saat Ini &rarr;
                            </a>
                        </div>
                    </div>

                    <!-- URL Manual (Opsional) -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                            Atau Masukkan URL File Langsung (Opsional / Cloud Storage Link)
                        </label>
                        <input
                            v-model="form.file_path"
                            type="text"
                            placeholder="https://res.cloudinary.com/... atau /storage/legalities/..."
                            class="w-full rounded-2xl border-gray-200 text-sm focus:border-brand-500 focus:ring-brand-500 shadow-2xs font-mono"
                        />
                        <p class="mt-1 text-[11px] text-gray-400">Gunakan ini jika berkas disimpan di CDN atau hosting eksternal.</p>
                        <p v-if="form.errors.file_path" class="mt-1 text-xs text-rose-500">{{ form.errors.file_path }}</p>
                    </div>

                    <!-- Order & Status -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 pt-4 border-t border-gray-100">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                                Urutan Tampilan
                            </label>
                            <input
                                v-model.number="form.order"
                                type="number"
                                min="1"
                                class="w-full rounded-2xl border-gray-200 text-sm focus:border-brand-500 focus:ring-brand-500 shadow-2xs"
                            />
                            <p class="mt-1 text-[11px] text-gray-400">Angka lebih kecil tampil lebih dulu di depan.</p>
                            <p v-if="form.errors.order" class="mt-1 text-xs text-rose-500">{{ form.errors.order }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                                Status Publikasi
                            </label>
                            <div class="mt-2 flex items-center gap-3">
                                <label class="inline-flex items-center gap-2 cursor-pointer">
                                    <input
                                        v-model="form.is_active"
                                        type="checkbox"
                                        class="h-4 w-4 rounded-md border-gray-300 text-brand-600 focus:ring-brand-500"
                                    />
                                    <span class="text-sm font-semibold text-gray-700">Tampilkan di Beranda Web (Aktif)</span>
                                </label>
                            </div>
                            <p v-if="form.errors.is_active" class="mt-1 text-xs text-rose-500">{{ form.errors.is_active }}</p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-3">
                    <Link
                        :href="route('admin.legalities.index')"
                        class="rounded-2xl border border-gray-300 bg-white px-5 py-2.5 text-xs font-bold text-gray-700 shadow-2xs hover:bg-gray-50 transition"
                    >
                        Batal
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center gap-2 rounded-2xl bg-brand-500 px-6 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-brand-600 transition disabled:opacity-50 cursor-pointer"
                    >
                        <svg v-if="form.processing" class="h-4 w-4 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span>{{ isEditing ? 'Simpan Perubahan' : 'Terbitkan Dokumen Legalitas' }}</span>
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
