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
    _method: isEdit.value ? 'PUT' : 'POST',
    name: props.product?.name ?? '',
    slug: props.product?.slug ?? '',
    description: props.product?.description ?? '',
    price: props.product?.price != null ? String(props.product.price) : '',
    weight_estimate_kg:
        props.product?.weight_estimate_kg != null
            ? String(props.product.weight_estimate_kg)
            : '',
    stock: props.product?.stock != null ? String(props.product.stock) : '0',
    max_sohibul: props.product?.max_sohibul != null ? Number(props.product.max_sohibul) : 1,
    primary_image_url: props.product?.primary_image_url ?? '',
    image_file: null,
    is_active: props.product?.is_active ?? true,
    service_ids: props.product?.services?.map((service) => service.id) ?? [],
});

const imagePreview = ref(props.product?.primary_image_url ?? null);

const handleFileChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.image_file = file;
        imagePreview.value = URL.createObjectURL(file);
    }
};

const toggleService = (serviceId) => {
    const idx = form.service_ids.indexOf(serviceId);
    if (idx > -1) {
        form.service_ids.splice(idx, 1);
    } else {
        form.service_ids.push(serviceId);
    }
};

const submit = () => {
    if (isEdit.value) {
        form.post(route('admin.produk.update', props.product.id), {
            forceFormData: true,
        });
    } else {
        form.post(route('admin.produk.store'), {
            forceFormData: true,
        });
    }
};
</script>

<template>
    <Head :title="isEdit ? `Ubah Produk: ${product.name}` : 'Tambah Produk Hewan Baru'" />

    <AdminLayout>
        <template #header>
            <div class="flex items-center gap-2">
                <Link :href="route('admin.produk.index')" class="text-xs font-semibold text-gray-500 hover:text-brand-600">
                    &larr; Kembali ke Katalog Hewan
                </Link>
                <span class="text-xs text-gray-300">/</span>
                <span class="text-xs font-bold text-gray-800">{{ isEdit ? 'Ubah Produk' : 'Produk Baru' }}</span>
            </div>
            <h2 class="mt-1 text-xl font-bold leading-tight text-gray-900">
                {{ isEdit ? `Ubah Produk: ${product.name}` : 'Tambah Produk Hewan Baru' }}
            </h2>
        </template>

        <div class="mx-auto max-w-3xl">
            <form
                class="space-y-6 rounded-2xl border border-gray-200/80 bg-white p-6 sm:p-8 shadow-sm"
                @submit.prevent="submit"
            >
                <!-- 1. Informasi Utama Hewan -->
                <div class="border-b border-gray-100 pb-5">
                    <h3 class="text-base font-bold text-gray-900">Informasi Hewan Qurban / Aqiqah</h3>
                    <p class="text-xs text-gray-500">Isi identitas nama hewan, estimasi bobot, dan deskripsi syariat.</p>

                    <div class="mt-5 space-y-4">
                        <div>
                            <InputLabel for="name" value="Nama Produk Hewan *" class="!text-xs font-bold" />
                            <TextInput
                                id="name"
                                v-model="form.name"
                                type="text"
                                class="mt-1 block w-full !rounded-xl !text-xs"
                                placeholder="Contoh: Domba Premium Standar A / Sapi Limosin 1/7"
                                required
                                autofocus
                            />
                            <InputError class="mt-1" :message="form.errors.name" />
                        </div>

                        <div>
                            <InputLabel for="slug" value="Slug Kustom (Opsional)" class="!text-xs font-bold" />
                            <TextInput
                                id="slug"
                                v-model="form.slug"
                                type="text"
                                class="mt-1 block w-full !rounded-xl !text-xs font-mono"
                                placeholder="domba-premium-a"
                            />
                            <InputError class="mt-1" :message="form.errors.slug" />
                            <p class="mt-1 text-[11px] text-gray-400">Kosongkan jika ingin dibuat otomatis dari nama produk.</p>
                        </div>

                        <div>
                            <InputLabel for="description" value="Deskripsi & Spesifikasi Hewan" class="!text-xs font-bold" />
                            <textarea
                                id="description"
                                v-model="form.description"
                                rows="3"
                                placeholder="Jelaskan kondisi fisik hewan, kesehatan, usia sesuai syariat, dan paket pemrosesan..."
                                class="mt-1 block w-full rounded-xl border-gray-300 text-xs shadow-sm focus:border-brand-500 focus:ring-brand-500"
                            ></textarea>
                            <InputError class="mt-1" :message="form.errors.description" />
                        </div>
                    </div>
                </div>

                <!-- 2. Harga, Bobot, & Stok -->
                <div class="border-b border-gray-100 pb-5">
                    <h3 class="text-base font-bold text-gray-900">Harga, Bobot, & Ketersediaan</h3>
                    <p class="text-xs text-gray-500">Tentukan harga satuan jual, perkiraan berat hidup, dan jumlah stok tersedia.</p>

                    <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <div>
                            <InputLabel for="price" value="Harga Satuan (Rp) *" class="!text-xs font-bold" />
                            <div class="relative mt-1">
                                <span class="absolute left-3 top-2.5 text-xs font-bold text-gray-400">Rp</span>
                                <TextInput
                                    id="price"
                                    v-model="form.price"
                                    type="number"
                                    min="0"
                                    step="1"
                                    class="block w-full !rounded-xl !text-xs !pl-9 font-bold text-brand-600"
                                    placeholder="2500000"
                                    required
                                />
                            </div>
                            <InputError class="mt-1" :message="form.errors.price" />
                        </div>

                        <div>
                            <InputLabel for="weight_estimate_kg" value="Estimasi Bobot (kg)" class="!text-xs font-bold" />
                            <div class="relative mt-1">
                                <TextInput
                                    id="weight_estimate_kg"
                                    v-model="form.weight_estimate_kg"
                                    type="number"
                                    min="0"
                                    step="0.1"
                                    class="block w-full !rounded-xl !text-xs !pr-9"
                                    placeholder="28.5"
                                />
                                <span class="absolute right-3 top-2.5 text-xs text-gray-400">kg</span>
                            </div>
                            <InputError class="mt-1" :message="form.errors.weight_estimate_kg" />
                        </div>

                        <div>
                            <InputLabel for="stock" value="Stok Hewan (Ekor) *" class="!text-xs font-bold" />
                            <div class="relative mt-1">
                                <TextInput
                                    id="stock"
                                    v-model="form.stock"
                                    type="number"
                                    min="0"
                                    step="1"
                                    class="block w-full !rounded-xl !text-xs !pr-12"
                                    placeholder="10"
                                    required
                                />
                                <span class="absolute right-3 top-2.5 text-xs text-gray-400">ekor</span>
                            </div>
                            <InputError class="mt-1" :message="form.errors.stock" />
                        </div>

                        <div>
                            <InputLabel for="max_sohibul" value="Batas Maks. Sohibul / Ekor *" class="!text-xs font-bold" />
                            <div class="relative mt-1">
                                <TextInput
                                    id="max_sohibul"
                                    v-model="form.max_sohibul"
                                    type="number"
                                    min="1"
                                    max="50"
                                    step="1"
                                    class="block w-full !rounded-xl !text-xs !pr-14 font-semibold text-brand-700"
                                    placeholder="1 (Kambing) / 7 (Sapi)"
                                    required
                                />
                                <span class="absolute right-3 top-2.5 text-xs text-gray-400">orang</span>
                            </div>
                            <p class="mt-1 text-[10px] text-gray-400">Contoh: Kambing = 1 orang, Sapi = 7 orang.</p>
                            <InputError class="mt-1" :message="form.errors.max_sohibul" />
                        </div>
                    </div>
                </div>

                <!-- 3. Foto Produk Hewan -->
                <div class="border-b border-gray-100 pb-5">
                    <h3 class="text-base font-bold text-gray-900">Foto Produk Hewan</h3>
                    <p class="text-xs text-gray-500">Unggah foto dokumentasi hewan berkualitas baik.</p>

                    <div class="mt-5 rounded-2xl border border-gray-200/80 bg-gray-50/50 p-5 space-y-4">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <InputLabel for="image_file" value="Upload File Foto Hewan" class="!text-xs font-bold" />
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
                                <InputLabel for="primary_image_url" value="Atau Input Link URL Foto" class="!text-xs font-bold" />
                                <TextInput
                                    id="primary_image_url"
                                    v-model="form.primary_image_url"
                                    type="text"
                                    class="mt-1 block w-full !rounded-xl !text-xs"
                                    placeholder="https://..."
                                />
                                <InputError class="mt-1" :message="form.errors.primary_image_url" />
                                <p class="mt-1 text-[11px] text-gray-400">Opsional bila tidak menggunakan file upload.</p>
                            </div>
                        </div>

                        <!-- Live Preview Foto Hewan -->
                        <div v-if="imagePreview || form.primary_image_url" class="mt-3 flex items-center gap-4">
                            <div class="h-24 w-24 shrink-0 overflow-hidden rounded-xl border border-gray-200 bg-white">
                                <img
                                    :src="imagePreview || form.primary_image_url"
                                    alt="Preview Hewan"
                                    class="h-full w-full object-cover"
                                />
                            </div>
                            <div class="text-xs text-gray-500">
                                <p class="font-bold text-gray-700">Preview Foto Hewan</p>
                                <p class="text-[11px] text-gray-400 mt-0.5">Foto ini akan menjadi tampilan utama pada katalog layanan pembeli.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. Pointing Layanan Terkait -->
                <div class="border-b border-gray-100 pb-5">
                    <h3 class="text-base font-bold text-gray-900">Tautkan ke Layanan (Pointing)</h3>
                    <p class="text-xs text-gray-500">Pilih satu atau lebih layanan yang menyediakan pilihan hewan ini.</p>

                    <div class="mt-4 grid grid-cols-1 gap-2.5 sm:grid-cols-2">
                        <div
                            v-for="service in services"
                            :key="service.id"
                            @click="toggleService(service.id)"
                            class="cursor-pointer flex items-center gap-3 rounded-xl border p-3.5 transition"
                            :class="
                                form.service_ids.includes(service.id)
                                    ? 'border-brand-500 bg-brand-50/50 text-brand-900'
                                    : 'border-gray-200 bg-white hover:bg-gray-50 text-gray-700'
                            "
                        >
                            <input
                                type="checkbox"
                                :checked="form.service_ids.includes(service.id)"
                                class="rounded border-gray-300 text-brand-600 focus:ring-brand-500"
                                @click.stop
                                @change="toggleService(service.id)"
                            />
                            <div>
                                <p class="text-xs font-bold">{{ service.name }}</p>
                                <!-- <p class="text-[11px] text-gray-400">/layanan/{{ service.slug }}</p> -->
                            </div>
                        </div>
                    </div>
                    <InputError class="mt-2" :message="form.errors.service_ids" />
                </div>

                <!-- 5. Status Publikasi -->
                <div class="pt-1">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <Checkbox v-model:checked="form.is_active" class="!rounded-md" />
                        <div>
                            <span class="text-xs font-bold text-gray-900 block">
                                Publikasikan Produk Hewan
                            </span>
                            <span class="text-[11px] text-gray-500 block">
                                Produk akan tampil aktif dan dapat dipesan oleh pembeli di layanan terkait.
                            </span>
                        </div>
                    </label>
                    <InputError class="mt-1" :message="form.errors.is_active" />
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-5">
                    <Link :href="route('admin.produk.index')">
                        <SecondaryButton type="button" class="!rounded-xl !text-xs !py-2.5">
                            Batal
                        </SecondaryButton>
                    </Link>
                    <PrimaryButton
                        class="!rounded-xl !text-xs !py-2.5 !bg-brand-500 hover:!bg-brand-600 shadow-sm"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Menyimpan...' : (isEdit ? 'Simpan Perubahan' : 'Buat Produk Hewan') }}
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
