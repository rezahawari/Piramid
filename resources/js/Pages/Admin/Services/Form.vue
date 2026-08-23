<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
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
    <Head :title="isEdit ? 'Ubah Layanan' : 'Tambah Layanan'" />

    <AdminLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ isEdit ? `Ubah Layanan: ${service.name}` : 'Tambah Layanan' }}
            </h2>
        </template>

        <div class="mx-auto max-w-2xl">
            <form
                class="space-y-6 bg-white p-6 shadow-sm sm:rounded-lg"
                @submit.prevent="submit"
            >
                <div>
                    <InputLabel for="name" value="Nama" />
                    <TextInput
                        id="name"
                        v-model="form.name"
                        type="text"
                        class="mt-1 block w-full"
                        required
                        autofocus
                    />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <div>
                    <InputLabel for="slug" value="Slug" />
                    <TextInput
                        id="slug"
                        v-model="form.slug"
                        type="text"
                        class="mt-1 block w-full"
                        placeholder="Kosongkan untuk membuat otomatis dari nama"
                    />
                    <InputError class="mt-2" :message="form.errors.slug" />
                </div>

                <div>
                    <InputLabel for="description" value="Deskripsi" />
                    <textarea
                        id="description"
                        v-model="form.description"
                        rows="4"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500"
                    ></textarea>
                    <InputError class="mt-2" :message="form.errors.description" />
                </div>

                <div class="space-y-4 rounded-lg border border-gray-200 bg-gray-50/50 p-4">
                    <span class="block text-sm font-medium text-gray-700">Gambar Sampul Layanan</span>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <InputLabel for="image_file" value="Upload File Gambar" />
                            <input
                                id="image_file"
                                type="file"
                                accept="image/*"
                                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:rounded-md file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-brand-700 hover:file:bg-brand-100"
                                @change="handleFileChange"
                            />
                            <InputError class="mt-2" :message="form.errors.image_file" />
                            <p class="mt-1 text-xs text-gray-500">Maks. 5MB (PNG, JPG, JPEG, WEBP)</p>
                        </div>

                        <div>
                            <InputLabel for="cover_image_url" value="Atau Link URL Gambar (Opsional)" />
                            <TextInput
                                id="cover_image_url"
                                v-model="form.cover_image_url"
                                type="text"
                                class="mt-1 block w-full"
                                placeholder="https://..."
                            />
                            <InputError class="mt-2" :message="form.errors.cover_image_url" />
                            <p class="mt-1 text-xs text-gray-500">Digunakan jika tidak memilih upload file</p>
                        </div>
                    </div>

                    <!-- Preview Gambar -->
                    <div v-if="imagePreview || form.cover_image_url" class="mt-3 flex items-center gap-4">
                        <div class="h-20 w-28 overflow-hidden rounded-md border border-gray-200 bg-white">
                            <img
                                :src="imagePreview || form.cover_image_url"
                                alt="Preview Sampul"
                                class="h-full w-full object-cover"
                            />
                        </div>
                        <div class="text-xs text-gray-500">
                            Preview tampilan gambar sampul layanan
                        </div>
                    </div>
                </div>

                <div>
                    <label class="flex items-center gap-2">
                        <Checkbox v-model:checked="form.is_active" />
                        <span class="text-sm text-gray-700">
                            Aktif (tampil di halaman pengguna)
                        </span>
                    </label>
                    <InputError class="mt-2" :message="form.errors.is_active" />
                </div>

                <div class="flex items-center justify-end gap-3">
                    <Link :href="route('admin.layanan.index')">
                        <SecondaryButton type="button">Batal</SecondaryButton>
                    </Link>
                    <PrimaryButton :disabled="form.processing">Simpan</PrimaryButton>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
