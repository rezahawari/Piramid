<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    product: {
        type: Object,
        default: null,
    },
    services: {
        type: Array,
        required: true,
    },
});

const isEdit = computed(() => props.product !== null);

const form = useForm({
    name: props.product?.name ?? '',
    slug: props.product?.slug ?? '',
    description: props.product?.description ?? '',
    price: props.product?.price != null ? String(props.product.price) : '',
    weight_estimate_kg:
        props.product?.weight_estimate_kg != null
            ? String(props.product.weight_estimate_kg)
            : '',
    stock: props.product?.stock != null ? String(props.product.stock) : '0',
    primary_image_url: props.product?.primary_image_url ?? '',
    is_active: props.product?.is_active ?? true,
    service_ids: props.product?.services?.map((service) => service.id) ?? [],
});

const submit = () => {
    if (isEdit.value) {
        form.put(route('admin.produk.update', props.product.id));
    } else {
        form.post(route('admin.produk.store'));
    }
};
</script>

<template>
    <Head :title="isEdit ? 'Ubah Produk' : 'Tambah Produk'" />

    <AdminLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ isEdit ? `Ubah Produk: ${product.name}` : 'Tambah Produk' }}
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

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                    <div>
                        <InputLabel for="price" value="Harga (Rp)" />
                        <TextInput
                            id="price"
                            v-model="form.price"
                            type="number"
                            min="0"
                            step="0.01"
                            class="mt-1 block w-full"
                            required
                        />
                        <InputError class="mt-2" :message="form.errors.price" />
                    </div>

                    <div>
                        <InputLabel for="weight_estimate_kg" value="Estimasi Berat (kg)" />
                        <TextInput
                            id="weight_estimate_kg"
                            v-model="form.weight_estimate_kg"
                            type="number"
                            min="0"
                            step="0.01"
                            class="mt-1 block w-full"
                        />
                        <InputError class="mt-2" :message="form.errors.weight_estimate_kg" />
                    </div>

                    <div>
                        <InputLabel for="stock" value="Stok" />
                        <TextInput
                            id="stock"
                            v-model="form.stock"
                            type="number"
                            min="0"
                            step="1"
                            class="mt-1 block w-full"
                            required
                        />
                        <InputError class="mt-2" :message="form.errors.stock" />
                    </div>
                </div>

                <div>
                    <InputLabel for="primary_image_url" value="URL Gambar Utama" />
                    <TextInput
                        id="primary_image_url"
                        v-model="form.primary_image_url"
                        type="text"
                        class="mt-1 block w-full"
                        placeholder="https://..."
                    />
                    <InputError class="mt-2" :message="form.errors.primary_image_url" />
                </div>

                <div>
                    <InputLabel value="Layanan (Pointing)" />
                    <p class="mt-1 text-sm text-gray-500">
                        Satu produk dapat ditautkan ke beberapa layanan.
                    </p>
                    <div class="mt-3 space-y-2">
                        <label
                            v-for="service in services"
                            :key="service.id"
                            class="flex items-center gap-2"
                        >
                            <Checkbox v-model:checked="form.service_ids" :value="service.id" />
                            <span class="text-sm text-gray-700">{{ service.name }}</span>
                        </label>
                    </div>
                    <InputError class="mt-2" :message="form.errors.service_ids" />
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
                    <Link :href="route('admin.produk.index')">
                        <SecondaryButton type="button">Batal</SecondaryButton>
                    </Link>
                    <PrimaryButton :disabled="form.processing">Simpan</PrimaryButton>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
