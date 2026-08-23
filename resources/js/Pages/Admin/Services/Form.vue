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
    service: {
        type: Object,
        default: null,
    },
});

const isEdit = computed(() => props.service !== null);

const form = useForm({
    _method: isEdit.value ? 'PUT' : 'POST',
    name: props.service?.name ?? '',
    slug: props.service?.slug ?? '',
    description: props.service?.description ?? '',
    cover_image_url: props.service?.cover_image_url ?? '',
    image_file: null,
    is_active: props.service?.is_active ?? true,
});

const imagePreview = ref(props.service?.cover_image_url ?? null);

const handleFileChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.image_file = file;
        imagePreview.value = URL.createObjectURL(file);
    }
};

const submit = () => {
    if (isEdit.value) {
        form.post(route('admin.layanan.update', props.service.id), {
            forceFormData: true,
        });
    } else {
        form.post(route('admin.layanan.store'), {
            forceFormData: true,
        });
    }
};
</script>

<template>
    <Head :title="isEdit ? `Ubah Layanan: ${service.name}` : 'Tambah Layanan Baru'" />

    <AdminLayout>
        <template #header>
            <div class="flex items-center gap-2">
                <Link :href="route('admin.layanan.index')" class="text-xs font-semibold text-gray-500 hover:text-brand-600">
                    &larr; Kembali ke Layanan
                </Link>
                <span class="text-xs text-gray-300">/</span>
                <span class="text-xs font-bold text-gray-800">{{ isEdit ? 'Ubah Layanan' : 'Layanan Baru' }}</span>
            </div>
            <h2 class="mt-1 text-xl font-bold leading-tight text-gray-900">
                {{ isEdit ? `Ubah Layanan: ${service.name}` : 'Tambah Layanan Baru' }}
            </h2>
        </template>

        <div class="mx-auto max-w-3xl">
            <form
                class="space-y-6 rounded-2xl border border-gray-200/80 bg-white p-6 sm:p-8 shadow-sm"
                @submit.prevent="submit"
            >
                <!-- Informasi Utama -->
                <div class="border-b border-gray-100 pb-5">
                    <h3 class="text-base font-bold text-gray-900">Informasi Layanan</h3>
                    <p class="text-xs text-gray-500">Isi detail nama dan identifikasi layanan qurban/aqiqah.</p>

                    <div class="mt-5 space-y-4">
                        <div>
                            <InputLabel for="name" value="Nama Layanan *" class="!text-xs font-bold" />
                            <TextInput
                                id="name"
                                v-model="form.name"
                                type="text"
                                class="mt-1 block w-full !rounded-xl !text-xs"
                                placeholder="Contoh: Qurban Pelosok Negeri / Aqiqah Berkah"
                                required
                                autofocus
                            />
                            <InputError class="mt-1" :message="form.errors.name" />
                        </div>

                        <div>
                            <InputLabel for="slug" value="Slug Kustom (Opsional)" class="!text-xs font-bold" />
                            <div class="relative mt-1">
                                <span class="absolute left-3.5 top-2.5 text-xs text-gray-400 font-mono">/layanan/</span>
                                <TextInput
                                    id="slug"
                                    v-model="form.slug"
                                    type="text"
                                    class="block w-full !rounded-xl !text-xs !pl-20 font-mono"
                                    placeholder="qurban-pelosok"
                                />
                            </div>
                            <InputError class="mt-1" :message="form.errors.slug" />
                            <p class="mt-1 text-[11px] text-gray-400">Kosongkan jika ingin dibuat otomatis dari nama layanan.</p>
                        </div>

                        <div>
                            <InputLabel for="description" value="Deskripsi Lengkap" class="!text-xs font-bold" />
                            <textarea
                                id="description"
                                v-model="form.description"
                                rows="4"
                                placeholder="Jelaskan rincian keutamaan, alur penyaluran, dan manfaat layanan ini..."
                                class="mt-1 block w-full rounded-xl border-gray-300 text-xs shadow-sm focus:border-brand-500 focus:ring-brand-500"
                            ></textarea>
                            <InputError class="mt-1" :message="form.errors.description" />
                        </div>
                    </div>
                </div>

                <!-- Foto Sampul Layanan -->
                <div class="border-b border-gray-100 pb-5">
                    <h3 class="text-base font-bold text-gray-900">Foto Sampul Layanan</h3>
                    <p class="text-xs text-gray-500">Unggah foto banner yang menarik untuk ditampilkan pada katalog dan header.</p>

                    <div class="mt-5 rounded-2xl border border-gray-200/80 bg-gray-50/50 p-5 space-y-4">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <InputLabel for="image_file" value="Upload File Foto" class="!text-xs font-bold" />
                                <input
                                    id="image_file"
                                    type="file"
                                    accept="image/*"
                                    class="mt-1 block w-full text-xs text-gray-500 file:mr-3 file:rounded-xl file:border-0 file:bg-brand-50 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-brand-700 hover:file:bg-brand-100"
                                    @change="handleFileChange"
                                />
                                <InputError class="mt-1" :message="form.errors.image_file" />
                                <p class="mt-1 text-[11px] text-gray-400">Format: PNG, JPG, JPEG, WEBP (Maks. 5MB)</p>
                            </div>

                            <div>
                                <InputLabel for="cover_image_url" value="Atau Input URL Foto" class="!text-xs font-bold" />
                                <TextInput
                                    id="cover_image_url"
                                    v-model="form.cover_image_url"
                                    type="text"
                                    class="mt-1 block w-full !rounded-xl !text-xs"
                                    placeholder="https://..."
                                />
                                <InputError class="mt-1" :message="form.errors.cover_image_url" />
                                <p class="mt-1 text-[11px] text-gray-400">Opsional bila tidak menggunakan file upload.</p>
                            </div>
                        </div>

                        <!-- Live Preview Banner -->
                        <div v-if="imagePreview || form.cover_image_url" class="mt-3">
                            <span class="text-xs font-semibold text-gray-600 block mb-1.5">Preview Banner:</span>
                            <div class="relative h-44 w-full overflow-hidden rounded-xl border border-gray-200 bg-white">
                                <img
                                    :src="imagePreview || form.cover_image_url"
                                    alt="Preview Sampul"
                                    class="h-full w-full object-cover"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status Publikasi -->
                <div class="pt-1">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <Checkbox v-model:checked="form.is_active" class="!rounded-md" />
                        <div>
                            <span class="text-xs font-bold text-gray-900 block">
                                Publikasikan Layanan
                            </span>
                            <span class="text-[11px] text-gray-500 block">
                                Layanan akan tampil aktif di navbar dan dapat dipesan oleh pengunjung.
                            </span>
                        </div>
                    </label>
                    <InputError class="mt-1" :message="form.errors.is_active" />
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-5">
                    <Link :href="route('admin.layanan.index')">
                        <SecondaryButton type="button" class="!rounded-xl !text-xs !py-2.5">
                            Batal
                        </SecondaryButton>
                    </Link>
                    <PrimaryButton
                        class="!rounded-xl !text-xs !py-2.5 !bg-brand-500 hover:!bg-brand-600 shadow-sm"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Menyimpan...' : (isEdit ? 'Simpan Perubahan' : 'Buat Layanan') }}
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
