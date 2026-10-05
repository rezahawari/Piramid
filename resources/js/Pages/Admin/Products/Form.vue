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

const initialVariants = props.product?.variants?.length
    ? props.product.variants.map((v) => ({
          id: v.id,
          name_id: v.name_id,
          name_en: v.name_en || '',
          name_zh: v.name_zh || '',
          name_ar: v.name_ar || '',
          spec_description: v.spec_description || '',
          price_idr: String(v.price_idr),
          price_usd: v.price_usd != null ? String(v.price_usd) : '',
          price_cny: v.price_cny != null ? String(v.price_cny) : '',
          price_sar: v.price_sar != null ? String(v.price_sar) : '',
          stock: String(v.stock),
          is_active: v.is_active ?? true,
      }))
    : [];

const form = useForm({
    _method: isEdit.value ? 'PUT' : 'POST',
    name: props.product?.name ?? '',
    slug: props.product?.slug ?? '',
    description: props.product?.description ?? '',
    price: props.product?.price != null ? String(props.product.price) : '',
    price_usd: props.product?.price_usd != null ? String(props.product.price_usd) : '',
    price_cny: props.product?.price_cny != null ? String(props.product.price_cny) : '',
    price_sar: props.product?.price_sar != null ? String(props.product.price_sar) : '',
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
    variants: initialVariants,
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

const addVariant = () => {
    form.variants.push({
        id: null,
        name_id: '',
        name_en: '',
        name_zh: '',
        name_ar: '',
        spec_description: '',
        price_idr: form.price || '0',
        price_usd: form.price_usd || '',
        price_cny: form.price_cny || '',
        price_sar: form.price_sar || '',
        stock: '10',
        is_active: true,
    });
};

const removeVariant = (index) => {
    form.variants.splice(index, 1);
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

        <div class="mx-auto max-w-4xl">
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
                                placeholder="Contoh: Domba Premium Standar / Sapi Limosin"
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
                                placeholder="domba-premium-standar"
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

                <!-- 2. Harga Dasar (Fallback / Mulai dari) & Multi-Currency -->
                <div class="border-b border-gray-100 pb-5">
                    <h3 class="text-base font-bold text-gray-900">Harga Acuan Dasar (Mulai dari)</h3>
                    <p class="text-xs text-gray-500">Harga dasar produk jika tidak memilih varian atau sebagai display card terendah.</p>

                    <div class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-4">
                        <div>
                            <InputLabel for="price" value="Harga IDR (Rp) *" class="!text-xs font-bold" />
                            <TextInput
                                id="price"
                                v-model="form.price"
                                type="number"
                                min="0"
                                class="mt-1 block w-full !rounded-xl !text-xs font-bold text-brand-600"
                                placeholder="2500000"
                                required
                            />
                            <InputError class="mt-1" :message="form.errors.price" />
                        </div>
                        <div>
                            <InputLabel for="price_usd" value="Harga USD ($)" class="!text-xs font-bold" />
                            <TextInput
                                id="price_usd"
                                v-model="form.price_usd"
                                type="number"
                                min="0"
                                step="0.01"
                                class="mt-1 block w-full !rounded-xl !text-xs font-semibold"
                                placeholder="165"
                            />
                        </div>
                        <div>
                            <InputLabel for="price_cny" value="Harga CNY (¥)" class="!text-xs font-bold" />
                            <TextInput
                                id="price_cny"
                                v-model="form.price_cny"
                                type="number"
                                min="0"
                                step="0.01"
                                class="mt-1 block w-full !rounded-xl !text-xs font-semibold"
                                placeholder="1180"
                            />
                        </div>
                        <div>
                            <InputLabel for="price_sar" value="Harga SAR (﷼)" class="!text-xs font-bold" />
                            <TextInput
                                id="price_sar"
                                v-model="form.price_sar"
                                type="number"
                                min="0"
                                step="0.01"
                                class="mt-1 block w-full !rounded-xl !text-xs font-semibold"
                                placeholder="620"
                            />
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div>
                            <InputLabel for="weight_estimate_kg" value="Estimasi Bobot Rata-rata (kg)" class="!text-xs font-bold" />
                            <TextInput
                                id="weight_estimate_kg"
                                v-model="form.weight_estimate_kg"
                                type="number"
                                min="0"
                                step="0.1"
                                class="mt-1 block w-full !rounded-xl !text-xs"
                                placeholder="28.5"
                            />
                            <InputError class="mt-1" :message="form.errors.weight_estimate_kg" />
                        </div>
                        <div>
                            <InputLabel for="stock" value="Total Stok Fallback (Ekor) *" class="!text-xs font-bold" />
                            <TextInput
                                id="stock"
                                v-model="form.stock"
                                type="number"
                                min="0"
                                class="mt-1 block w-full !rounded-xl !text-xs"
                                placeholder="10"
                                required
                            />
                            <InputError class="mt-1" :message="form.errors.stock" />
                        </div>
                        <div>
                            <InputLabel for="max_sohibul" value="Batas Maks. Sohibul / Ekor *" class="!text-xs font-bold" />
                            <TextInput
                                id="max_sohibul"
                                v-model="form.max_sohibul"
                                type="number"
                                min="1"
                                max="50"
                                class="mt-1 block w-full !rounded-xl !text-xs font-semibold text-brand-700"
                                placeholder="1 (Kambing) / 7 (Sapi)"
                                required
                            />
                            <InputError class="mt-1" :message="form.errors.max_sohibul" />
                        </div>
                    </div>
                </div>

                <!-- 3. Dynamic Variants Repeater -->
                <div class="border-b border-gray-100 pb-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-bold text-gray-900">Varian Hewan Dinamis</h3>
                            <p class="text-xs text-gray-500">Tambahkan berbagai pilihan varian (bobot, kelas super/reguler, jenis ras) dengan harga dan stok spesifik.</p>
                        </div>
                        <button
                            type="button"
                            @click="addVariant"
                            class="inline-flex items-center gap-1 rounded-xl bg-brand-50 border border-brand-200 px-3 py-1.5 text-xs font-bold text-brand-700 hover:bg-brand-100 transition cursor-pointer"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Tambah Varian
                        </button>
                    </div>

                    <div v-if="form.variants.length" class="mt-4 space-y-4">
                        <div
                            v-for="(v, idx) in form.variants"
                            :key="idx"
                            class="rounded-2xl border border-gray-200 bg-gray-50/60 p-4 relative"
                        >
                            <div class="flex items-center justify-between pb-2 border-b border-gray-200/60 mb-3">
                                <span class="text-xs font-bold text-brand-900">Varian #{{ idx + 1 }}</span>
                                <button
                                    type="button"
                                    @click="removeVariant(idx)"
                                    class="text-xs font-bold text-rose-600 hover:text-rose-700 cursor-pointer"
                                >
                                    ✕ Hapus
                                </button>
                            </div>

                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
                                <div>
                                    <InputLabel :value="`Nama Varian (ID) *`" class="!text-[11px] font-bold" />
                                    <TextInput
                                        v-model="v.name_id"
                                        type="text"
                                        class="mt-1 block w-full !rounded-xl !text-xs bg-white"
                                        placeholder="Contoh: Tipe A (Super)"
                                        required
                                    />
                                </div>
                                <div>
                                    <InputLabel :value="`Nama Varian (EN)`" class="!text-[11px] font-bold" />
                                    <TextInput
                                        v-model="v.name_en"
                                        type="text"
                                        class="mt-1 block w-full !rounded-xl !text-xs bg-white"
                                        placeholder="e.g. Type A (Super)"
                                    />
                                </div>
                                <div>
                                    <InputLabel :value="`Nama (ZH Chinese)`" class="!text-[11px] font-bold" />
                                    <TextInput
                                        v-model="v.name_zh"
                                        type="text"
                                        class="mt-1 block w-full !rounded-xl !text-xs bg-white"
                                        placeholder="例如: A型（特级）"
                                    />
                                </div>
                                <div>
                                    <InputLabel :value="`Nama (AR Arabic)`" class="!text-[11px] font-bold" />
                                    <TextInput
                                        v-model="v.name_ar"
                                        type="text"
                                        dir="rtl"
                                        class="mt-1 block w-full !rounded-xl !text-xs bg-white"
                                        placeholder="مثال: فئة أ (ممتاز)"
                                    />
                                </div>
                            </div>

                            <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-3">
                                <div>
                                    <InputLabel value="Keterangan / Bobot Spesifikasi" class="!text-[11px] font-bold" />
                                    <TextInput
                                        v-model="v.spec_description"
                                        type="text"
                                        class="mt-1 block w-full !rounded-xl !text-xs bg-white"
                                        placeholder="Contoh: 35-40 kg, Jantan"
                                    />
                                </div>
                                <div>
                                    <InputLabel value="Stok Varian (Ekor) *" class="!text-[11px] font-bold" />
                                    <TextInput
                                        v-model="v.stock"
                                        type="number"
                                        min="0"
                                        class="mt-1 block w-full !rounded-xl !text-xs bg-white"
                                        required
                                    />
                                </div>
                                <div class="flex items-center pt-5">
                                    <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-gray-700">
                                        <Checkbox v-model:checked="v.is_active" />
                                        <span>Aktifkan Varian Ini</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Harga Varian Multi-Mata Uang -->
                            <div class="mt-3 border-t border-gray-200/60 pt-3">
                                <span class="text-[11px] font-bold text-gray-600 block mb-1.5">Harga Varian per Mata Uang:</span>
                                <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
                                    <div>
                                        <InputLabel value="Harga IDR (Rp) *" class="!text-[10px] font-bold" />
                                        <TextInput
                                            v-model="v.price_idr"
                                            type="number"
                                            min="0"
                                            class="mt-1 block w-full !rounded-xl !text-xs font-bold text-brand-700 bg-white"
                                            required
                                        />
                                    </div>
                                    <div>
                                        <InputLabel value="Harga USD ($)" class="!text-[10px] font-bold" />
                                        <TextInput
                                            v-model="v.price_usd"
                                            type="number"
                                            min="0"
                                            step="0.01"
                                            class="mt-1 block w-full !rounded-xl !text-xs bg-white"
                                        />
                                    </div>
                                    <div>
                                        <InputLabel value="Harga CNY (¥)" class="!text-[10px] font-bold" />
                                        <TextInput
                                            v-model="v.price_cny"
                                            type="number"
                                            min="0"
                                            step="0.01"
                                            class="mt-1 block w-full !rounded-xl !text-xs bg-white"
                                        />
                                    </div>
                                    <div>
                                        <InputLabel value="Harga SAR (﷼)" class="!text-[10px] font-bold" />
                                        <TextInput
                                            v-model="v.price_sar"
                                            type="number"
                                            min="0"
                                            step="0.01"
                                            class="mt-1 block w-full !rounded-xl !text-xs bg-white"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="mt-3 rounded-xl border border-dashed border-gray-200 bg-gray-50/40 p-4 text-center text-xs text-gray-500">
                        Belum ada varian ditambahkan. Produk akan menggunakan harga dasar di atas.
                    </div>
                </div>

                <!-- 4. Foto Produk Hewan -->
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

                <!-- 5. Pointing Layanan Terkait -->
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
                            </div>
                        </div>
                    </div>
                    <InputError class="mt-2" :message="form.errors.service_ids" />
                </div>

                <!-- 6. Status Publikasi -->
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
