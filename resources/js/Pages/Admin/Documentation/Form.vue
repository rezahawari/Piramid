<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    item: {
        type: Object,
        default: null,
    },
});

const isEdit = computed(() => props.item !== null);

const form = useForm({
    _method: isEdit.value ? 'PUT' : 'POST',
    title: props.item?.title ?? '',
    type: props.item?.type ?? 'image',
    media_file: null,
    file_url: props.item?.file_url ?? '',
    youtube_url: props.item?.youtube_url ?? '',
    description: props.item?.description ?? '',
    category: props.item?.category ?? 'qurban',
    order_index: props.item?.order_index ?? 0,
    is_active: props.item?.is_active ?? true,
});

const filePreview = ref(props.item?.file_url ?? null);

const handleFileChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.media_file = file;
        filePreview.value = URL.createObjectURL(file);
    }
};

const submit = () => {
    if (isEdit.value) {
        form.post(route('admin.galeri.update', props.item.id), {
            forceFormData: true,
        });
    } else {
        form.post(route('admin.galeri.store'), {
            forceFormData: true,
        });
    }
};
</script>

<template>
    <Head :title="isEdit ? `Ubah Media: ${item.title}` : 'Tambah Media Dokumentasi Baru'" />

    <AdminLayout>
        <template #header>
            <div class="flex items-center gap-2">
                <Link :href="route('admin.galeri.index')" class="text-xs font-semibold text-gray-500 hover:text-brand-600">
                    &larr; Kembali ke Galeri
                </Link>
                <span class="text-xs text-gray-300">/</span>
                <span class="text-xs font-bold text-gray-800">{{ isEdit ? 'Ubah Media' : 'Media Baru' }}</span>
            </div>
            <h2 class="mt-1 text-xl font-bold leading-tight text-gray-900">
                {{ isEdit ? `Ubah Media: ${item.title}` : 'Tambah Media Dokumentasi Baru' }}
            </h2>
        </template>

        <div class="mx-auto max-w-3xl">
            <form
                class="space-y-6 rounded-2xl border border-gray-200/80 bg-white p-6 sm:p-8 shadow-sm"
                @submit.prevent="submit"
            >
                <!-- Informasi Media -->
                <div class="border-b border-gray-100 pb-5">
                    <h3 class="text-base font-bold text-gray-900">Informasi Media</h3>
                    <p class="text-xs text-gray-500">Judul, tipe format media, dan kategori penempatan.</p>

                    <div class="mt-5 space-y-4">
                        <div>
                            <InputLabel for="title" value="Judul Dokumentasi / Video *" class="!text-xs font-bold" />
                            <TextInput
                                id="title"
                                v-model="form.title"
                                type="text"
                                class="mt-1 block w-full !rounded-xl !text-xs"
                                placeholder="Contoh: Proses Penyembelihan Syari di RPH / Dokumentasi Distribusi Pelosok"
                                required
                                autofocus
                            />
                            <InputError class="mt-1" :message="form.errors.title" />
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <InputLabel for="type" value="Tipe Format Media *" class="!text-xs font-bold" />
                                <select
                                    id="type"
                                    v-model="form.type"
                                    class="mt-1 block w-full rounded-xl border-gray-300 text-xs shadow-sm focus:border-brand-500 focus:ring-brand-500"
                                    required
                                >
                                    <option value="image">📷 Foto / Gambar</option>
                                    <option value="video">🎬 File Video Lokal (MP4 / WebM)</option>
                                    <option value="youtube">▶ Video YouTube (Embed Link)</option>
                                </select>
                                <InputError class="mt-1" :message="form.errors.type" />
                            </div>

                            <div>
                                <InputLabel for="category" value="Kategori / Topik *" class="!text-xs font-bold" />
                                <select
                                    id="category"
                                    v-model="form.category"
                                    class="mt-1 block w-full rounded-xl border-gray-300 text-xs shadow-sm focus:border-brand-500 focus:ring-brand-500"
                                    required
                                >
                                    <option value="qurban">Qurban</option>
                                    <option value="aqiqah">Aqiqah</option>
                                    <option value="edukasi">Edukasi Syariat</option>
                                    <option value="distribusi">Penyaluran & Distribusi</option>
                                </select>
                                <InputError class="mt-1" :message="form.errors.category" />
                            </div>
                        </div>

                        <div>
                            <InputLabel for="description" value="Deskripsi / Keterangan (Opsional)" class="!text-xs font-bold" />
                            <textarea
                                id="description"
                                v-model="form.description"
                                rows="3"
                                placeholder="Jelaskan ringkasan dokumentasi atau penjelasan materi edukasi..."
                                class="mt-1 block w-full rounded-xl border-gray-300 text-xs shadow-sm focus:border-brand-500 focus:ring-brand-500"
                            ></textarea>
                            <InputError class="mt-1" :message="form.errors.description" />
                        </div>
                    </div>
                </div>

                <!-- Media Upload / Link Section -->
                <div class="border-b border-gray-100 pb-5">
                    <h3 class="text-base font-bold text-gray-900">Berkas atau Tautan Media</h3>
                    <p class="text-xs text-gray-500">Sesuaikan dengan tipe format yang Anda pilih di atas.</p>

                    <div class="mt-5 rounded-2xl border border-gray-200/80 bg-gray-50/50 p-5 space-y-4">
                        <!-- Input Khusus YouTube -->
                        <div v-if="form.type === 'youtube'">
                            <InputLabel for="youtube_url" value="URL Embed Video YouTube *" class="!text-xs font-bold" />
                            <TextInput
                                id="youtube_url"
                                v-model="form.youtube_url"
                                type="text"
                                class="mt-1 block w-full !rounded-xl !text-xs"
                                placeholder="https://www.youtube-nocookie.com/embed/..."
                            />
                            <InputError class="mt-1" :message="form.errors.youtube_url" />
                            <p class="mt-1 text-[11px] text-gray-400">
                                Masukkan link embed YouTube (contoh: https://www.youtube-nocookie.com/embed/VIDEO_ID).
                            </p>
                        </div>

                        <!-- Input File / File URL untuk Foto & Video Lokal -->
                        <div v-else class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <InputLabel for="media_file" :value="form.type === 'video' ? 'Upload File Video' : 'Upload File Foto'" class="!text-xs font-bold" />
                                <input
                                    id="media_file"
                                    type="file"
                                    :accept="form.type === 'video' ? 'video/*' : 'image/*'"
                                    class="mt-1 block w-full text-xs text-gray-500 file:mr-3 file:rounded-xl file:border-0 file:bg-brand-50 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-brand-700 hover:file:bg-brand-100"
                                    @change="handleFileChange"
                                />
                                <InputError class="mt-1" :message="form.errors.media_file" />
                                <p class="mt-1 text-[11px] text-gray-400">Maks. 25MB</p>
                            </div>

                            <div>
                                <InputLabel for="file_url" value="Atau Input URL Media Langsung" class="!text-xs font-bold" />
                                <TextInput
                                    id="file_url"
                                    v-model="form.file_url"
                                    type="text"
                                    class="mt-1 block w-full !rounded-xl !text-xs"
                                    placeholder="https://..."
                                />
                                <InputError class="mt-1" :message="form.errors.file_url" />
                            </div>
                        </div>

                        <!-- Preview Area -->
                        <div v-if="filePreview || form.file_url || form.youtube_url" class="mt-3">
                            <span class="text-xs font-semibold text-gray-600 block mb-1.5">Pratinjau Media:</span>
                            <div class="relative h-44 w-full overflow-hidden rounded-xl border border-gray-200 bg-black/90 flex items-center justify-center">
                                <img
                                    v-if="form.type === 'image' && (filePreview || form.file_url)"
                                    :src="filePreview || form.file_url"
                                    alt="Preview"
                                    class="h-full w-full object-cover"
                                />
                                <video
                                    v-else-if="form.type === 'video' && (filePreview || form.file_url)"
                                    :src="filePreview || form.file_url"
                                    class="h-full w-full object-cover"
                                    controls
                                ></video>
                                <iframe
                                    v-else-if="form.type === 'youtube' && form.youtube_url"
                                    :src="form.youtube_url"
                                    class="h-full w-full"
                                ></iframe>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Urutan & Status -->
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <InputLabel for="order_index" value="Urutan Tampil (Order Index)" class="!text-xs font-bold" />
                        <TextInput
                            id="order_index"
                            v-model="form.order_index"
                            type="number"
                            min="0"
                            class="mt-1 block w-full !rounded-xl !text-xs"
                        />
                        <InputError class="mt-1" :message="form.errors.order_index" />
                    </div>

                    <div class="pt-6">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <Checkbox v-model:checked="form.is_active" class="!rounded-md" />
                            <div>
                                <span class="text-xs font-bold text-gray-900 block">
                                    Tampilkan di Landing Page
                                </span>
                                <span class="text-[11px] text-gray-500 block">
                                    Media akan dipublikasikan pada galeri halaman depan.
                                </span>
                            </div>
                        </label>
                        <InputError class="mt-1" :message="form.errors.is_active" />
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-5">
                    <Link :href="route('admin.galeri.index')">
                        <SecondaryButton type="button" class="!rounded-xl !text-xs !py-2.5">
                            Batal
                        </SecondaryButton>
                    </Link>
                    <PrimaryButton
                        class="!rounded-xl !text-xs !py-2.5 !bg-brand-500 hover:!bg-brand-600 shadow-sm"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Menyimpan...' : (isEdit ? 'Simpan Perubahan' : 'Buat Media') }}
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
