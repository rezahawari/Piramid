<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { currentLocale, formatCurrency, t } from '@/i18n';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const props = defineProps({
    service: {
        type: Object,
        required: true,
    },
    product: {
        type: Object,
        required: true,
    },
    distribution_options: {
        type: Array,
        required: true,
    },
    payment_options: {
        type: Array,
        required: true,
    },
});

const user = computed(() => usePage().props.auth?.user);

const hasVariants = computed(() => props.product.active_variants?.length > 0 || props.product.variants?.length > 0);
const variantsList = computed(() => props.product.active_variants ?? props.product.variants ?? []);

const selectedVariantId = ref(variantsList.value.length > 0 ? variantsList.value[0].id : null);
const selectedVariant = computed(() => variantsList.value.find((v) => v.id === selectedVariantId.value) || null);

const selectedDistributionId = ref(props.distribution_options.length > 0 ? props.distribution_options[0].id : null);
const selectedDistribution = computed(() => props.distribution_options.find((d) => d.id === selectedDistributionId.value) || null);

const selectedCookingOption = ref(props.service.default_cooking_option || 'raw');

// Mapping currency dari currentLocale
const currentCurrencyCode = computed(() => {
    const loc = currentLocale.value;
    if (loc === 'en') return 'USD';
    if (loc === 'zh') return 'CNY';
    if (loc === 'ar') return 'SAR';
    return 'IDR';
});

const form = useForm({
    service_id: props.service.id,
    product_id: props.product.id,
    product_variant_id: selectedVariantId.value,
    distribution_option_id: selectedDistributionId.value,
    cooking_option: selectedCookingOption.value,
    currency: currentCurrencyCode.value,
    quantity: 1,
    distribution_location_note: '',
    sohibul_names: [user.value?.name ?? ''],
    payment_method: 'midtrans',
});

// Update form saat pilihan berubah
watch(selectedVariantId, (val) => {
    form.product_variant_id = val;
});
watch(selectedDistributionId, (val) => {
    form.distribution_option_id = val;
});
watch(selectedCookingOption, (val) => {
    form.cooking_option = val;
});
watch(currentCurrencyCode, (val) => {
    form.currency = val;
});

const maxSohibulTotal = computed(() => form.quantity * (props.product.max_sohibul || 1));

const addSohibul = () => {
    if (form.sohibul_names.length < maxSohibulTotal.value) {
        form.sohibul_names.push('');
    }
};

const removeSohibul = (index) => {
    if (form.sohibul_names.length > 1) {
        form.sohibul_names.splice(index, 1);
    }
};

// Kalkulasi Harga Satuan Hewan
const unitPrice = computed(() => {
    const target = selectedVariant.value || props.product;
    const cur = currentCurrencyCode.value.toLowerCase();
    const priceKey = `price_${cur}`;
    const fallbackKey = 'price';
    const val = Number(target[priceKey] ?? target[fallbackKey] ?? 0);
    return val > 0 ? val : Number(target.price_idr ?? target.price ?? 0);
});

// Kalkulasi Biaya Olahan Tambahan (Hanya jika non-default)
const cookingFeePerUnit = computed(() => {
    if (!props.service.has_cooking_option) return 0;
    if (selectedCookingOption.value === props.service.default_cooking_option) return 0;
    
    const cur = currentCurrencyCode.value.toLowerCase();
    const feeKey = `cooking_fee_${cur}`;
    const val = Number(props.service[feeKey] ?? 0);
    return val > 0 ? val : Number(props.service.cooking_fee_idr ?? 0);
});

// Kalkulasi Biaya Distribusi Tambahan
const distributionFeePerUnit = computed(() => {
    if (!selectedDistribution.value) return 0;
    const cur = currentCurrencyCode.value.toLowerCase();
    const feeKey = `fee_${cur}`;
    const val = Number(selectedDistribution.value[feeKey] ?? 0);
    return val > 0 ? val : Number(selectedDistribution.value.fee_idr ?? 0);
});

const totalAmount = computed(() => {
    return form.quantity * (unitPrice.value + cookingFeePerUnit.value + distributionFeePerUnit.value);
});

const formatPriceDisplay = (amount) => {
    const cur = currentCurrencyCode.value;
    if (cur === 'USD') return `$${Number(amount).toLocaleString('en-US')}`;
    if (cur === 'CNY') return `¥${Number(amount).toLocaleString('zh-CN')}`;
    if (cur === 'SAR') return `﷼ ${Number(amount).toLocaleString('ar-SA')}`;
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(amount);
};

const getLocalizedDistName = (dist) => {
    const loc = currentLocale.value;
    return dist[`name_${loc}`] || dist.name_id;
};

const getLocalizedDistDesc = (dist) => {
    const loc = currentLocale.value;
    return dist[`description_${loc}`] || dist.description_id;
};

const getLocalizedVariantName = (v) => {
    const loc = currentLocale.value;
    return v[`name_${loc}`] || v.name_id;
};

const submit = () => form.post(route('checkout.store'));
</script>

<template>
    <Head :title="`Konfirmasi Pemesanan - ${product.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-bold leading-tight text-gray-900">Formulir Pemesanan Ibadah</h2>
                    <p class="text-xs text-gray-500">
                        Lengkapi rincian pesanan {{ service.name }} Anda dengan aman dan transparan.
                    </p>
                </div>
                <div class="flex items-center gap-2 text-xs text-gray-500">
                    <Link :href="route('catalog.index', service.slug)" class="hover:text-brand-600">
                        {{ service.name }}
                    </Link>
                    <span>/</span>
                    <span class="font-semibold text-gray-800">{{ product.name }}</span>
                </div>
            </div>
        </template>

        <div class="mx-auto max-w-5xl">
            <form @submit.prevent="submit" class="grid grid-cols-1 gap-8 lg:grid-cols-12">
                <!-- Left Column: Options & Inputs (7 Cols) -->
                <div class="space-y-6 lg:col-span-7">
                    <!-- 1. Ringkasan Hewan & Pilihan Varian -->
                    <div class="rounded-2xl border border-gray-200/80 bg-white p-5 sm:p-6 shadow-sm">
                        <div class="flex items-start gap-4">
                            <img
                                :src="product.primary_image_url || '/images/service-icons/sapi.jpg'"
                                :alt="product.name"
                                class="h-20 w-20 rounded-2xl object-cover border border-gray-100 shrink-0"
                            />
                            <div>
                                <span class="rounded-md bg-brand-50 px-2 py-0.5 text-[11px] font-bold text-brand-700 uppercase">
                                    {{ service.name }}
                                </span>
                                <h3 class="mt-1 text-base font-black text-gray-900">{{ product.name }}</h3>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    {{ product.weight_estimate_kg ? `Estimasi berat ~${Number(product.weight_estimate_kg)} Kg` : 'Sesuai standar syariat' }}
                                </p>
                            </div>
                        </div>

                        <!-- Varian Selector (Jika Ada) -->
                        <div v-if="hasVariants" class="mt-5 border-t border-gray-100 pt-4">
                            <InputLabel value="Pilih Varian Hewan *" class="!text-xs font-bold text-gray-900" />
                            <div class="mt-2.5 space-y-2.5">
                                <div
                                    v-for="v in variantsList"
                                    :key="v.id"
                                    @click="selectedVariantId = v.id"
                                    class="flex items-center justify-between rounded-xl border p-3.5 cursor-pointer transition"
                                    :class="
                                        selectedVariantId === v.id
                                            ? 'border-brand-500 bg-brand-50/60 ring-1 ring-brand-500'
                                            : 'border-gray-200 bg-white hover:bg-gray-50'
                                    "
                                >
                                    <div class="flex items-center gap-3">
                                        <input
                                            type="radio"
                                            :value="v.id"
                                            v-model="selectedVariantId"
                                            class="text-brand-600 focus:ring-brand-500"
                                        />
                                        <div>
                                            <p class="text-xs font-bold text-gray-900">{{ getLocalizedVariantName(v) }}</p>
                                            <p v-if="v.spec_description" class="text-[11px] text-gray-500">{{ v.spec_description }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-xs font-extrabold text-brand-700">
                                            {{ formatCurrency(v) }}
                                        </span>
                                        <span class="text-[10px] text-gray-400 block">Sisa stok: {{ v.stock }}</span>
                                    </div>
                                </div>
                            </div>
                            <InputError class="mt-1" :message="form.errors.product_variant_id" />
                        </div>

                        <!-- Quantity Selector -->
                        <div class="mt-5 border-t border-gray-100 pt-4 flex items-center justify-between">
                            <span class="text-xs font-bold text-gray-800">Jumlah Hewan:</span>
                            <div class="flex items-center gap-3">
                                <button
                                    type="button"
                                    @click="form.quantity > 1 ? form.quantity-- : null"
                                    class="h-8 w-8 rounded-lg border border-gray-200 bg-gray-50 font-bold text-gray-600 hover:bg-gray-100"
                                >
                                    -
                                </button>
                                <span class="text-sm font-black text-gray-900">{{ form.quantity }}</span>
                                <button
                                    type="button"
                                    @click="form.quantity++"
                                    class="h-8 w-8 rounded-lg border border-gray-200 bg-gray-50 font-bold text-gray-600 hover:bg-gray-100"
                                >
                                    +
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Opsi Pengolahan (Mentah / Matang) jika Layanan Aktif -->
                    <div v-if="service.has_cooking_option" class="rounded-2xl border border-gray-200/80 bg-white p-5 sm:p-6 shadow-sm">
                        <h3 class="text-sm font-bold text-gray-900">Bentuk Pengolahan Daging</h3>
                        <p class="text-xs text-gray-500">Pilih penyajian daging yang diinginkan.</p>

                        <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div
                                @click="selectedCookingOption = 'raw'"
                                class="rounded-xl border p-3.5 cursor-pointer transition"
                                :class="selectedCookingOption === 'raw' ? 'border-brand-500 bg-brand-50/60 ring-1 ring-brand-500' : 'border-gray-200 bg-white hover:bg-gray-50'"
                            >
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <input type="radio" value="raw" v-model="selectedCookingOption" class="text-brand-600 focus:ring-brand-500" />
                                        <span class="text-xs font-bold text-gray-900">Daging Mentah (Fresh)</span>
                                    </div>
                                    <span v-if="service.default_cooking_option === 'raw'" class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded">
                                        Termasuk
                                    </span>
                                    <span v-else class="text-[10px] font-bold text-brand-700">
                                        +{{ formatPriceDisplay(cookingFeePerUnit) }}
                                    </span>
                                </div>
                                <p class="mt-1 text-[11px] text-gray-500 pl-6">Dipotong higienis & dikemas per porsi paket.</p>
                            </div>

                            <div
                                @click="selectedCookingOption = 'cooked'"
                                class="rounded-xl border p-3.5 cursor-pointer transition"
                                :class="selectedCookingOption === 'cooked' ? 'border-brand-500 bg-brand-50/60 ring-1 ring-brand-500' : 'border-gray-200 bg-white hover:bg-gray-50'"
                            >
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <input type="radio" value="cooked" v-model="selectedCookingOption" class="text-brand-600 focus:ring-brand-500" />
                                        <span class="text-xs font-bold text-gray-900">Matang / Siap Saji</span>
                                    </div>
                                    <span v-if="service.default_cooking_option === 'cooked'" class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded">
                                        Termasuk
                                    </span>
                                    <span v-else class="text-[10px] font-bold text-brand-700">
                                        +{{ formatPriceDisplay(cookingFeePerUnit) }}
                                    </span>
                                </div>
                                <p class="mt-1 text-[11px] text-gray-500 pl-6">Diolah menjadi hidangan berkah siap santap.</p>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Pilihan Lokasi Penyaluran / Distribusi (Admin Dinamis) -->
                    <div class="rounded-2xl border border-gray-200/80 bg-white p-5 sm:p-6 shadow-sm">
                        <h3 class="text-sm font-bold text-gray-900">Tujuan Penyaluran / Distribusi Daging</h3>
                        <p class="text-xs text-gray-500">Pilih sasaran wilayah penyaluran amanah ibadah Anda.</p>

                        <div class="mt-3 space-y-3">
                            <div
                                v-for="dist in distribution_options"
                                :key="dist.id"
                                @click="selectedDistributionId = dist.id"
                                class="rounded-xl border p-3.5 cursor-pointer transition"
                                :class="selectedDistributionId === dist.id ? 'border-brand-500 bg-brand-50/60 ring-1 ring-brand-500' : 'border-gray-200 bg-white hover:bg-gray-50'"
                            >
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2.5">
                                        <input
                                            type="radio"
                                            :value="dist.id"
                                            v-model="selectedDistributionId"
                                            class="text-brand-600 focus:ring-brand-500"
                                        />
                                        <span class="text-xs font-bold text-gray-900">{{ getLocalizedDistName(dist) }}</span>
                                    </div>
                                    <span v-if="dist.fee_idr > 0 || dist.fee_usd > 0" class="text-[11px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded">
                                        +{{ formatCurrency(dist, 'fee') }}
                                    </span>
                                    <span v-else class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded">
                                        Gratis / Termasuk
                                    </span>
                                </div>
                                <p v-if="getLocalizedDistDesc(dist)" class="mt-1.5 text-[11px] text-gray-500 pl-6">
                                    {{ getLocalizedDistDesc(dist) }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-4">
                            <InputLabel for="note" value="Catatan / Pesan Khusus Penyaluran (Opsional)" class="!text-[11px] font-bold" />
                            <TextInput
                                id="note"
                                v-model="form.distribution_location_note"
                                type="text"
                                class="mt-1 block w-full !rounded-xl !text-xs"
                                placeholder="Contoh: Titip doa untuk kelancaran haji / keluarga"
                            />
                        </div>
                    </div>

                    <!-- 4. Nama-nama Sohibul (Jika Diaktifkan) -->
                    <div v-if="service.has_sohibul" class="rounded-2xl border border-gray-200/80 bg-white p-5 sm:p-6 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-bold text-gray-900">Atas Nama Sohibul (Pekurban)</h3>
                                <p class="text-xs text-gray-500">Maksimal {{ maxSohibulTotal }} nama.</p>
                            </div>
                            <button
                                v-if="form.sohibul_names.length < maxSohibulTotal"
                                type="button"
                                @click="addSohibul"
                                class="text-xs font-bold text-brand-600 hover:text-brand-700"
                            >
                                + Tambah Nama
                            </button>
                        </div>

                        <div class="mt-4 space-y-2.5">
                            <div
                                v-for="(name, index) in form.sohibul_names"
                                :key="index"
                                class="flex items-center gap-2"
                            >
                                <span class="text-xs font-bold text-gray-400 w-5">#{{ index + 1 }}</span>
                                <TextInput
                                    v-model="form.sohibul_names[index]"
                                    type="text"
                                    class="block w-full !rounded-xl !text-xs"
                                    :placeholder="`Nama Sohibul ${index + 1} (Bin/Binti ...)`"
                                    required
                                />
                                <button
                                    v-if="form.sohibul_names.length > 1"
                                    type="button"
                                    @click="removeSohibul(index)"
                                    class="text-rose-500 hover:text-rose-700 p-1 text-xs"
                                >
                                    ✕
                                </button>
                            </div>
                        </div>
                        <InputError class="mt-1" :message="form.errors.sohibul_names" />
                    </div>
                </div>

                <!-- Right Column: Order Summary & Checkout Action (5 Cols) -->
                <div class="space-y-6 lg:col-span-5">
                    <div class="rounded-2xl border border-gray-200/80 bg-white p-5 sm:p-6 shadow-sm sticky top-6">
                        <h3 class="text-base font-bold text-gray-900 border-b border-gray-100 pb-3">Rincian Pembayaran</h3>

                        <div class="mt-4 space-y-3 text-xs">
                            <div class="flex justify-between text-gray-600">
                                <span>{{ product.name }} {{ selectedVariant ? `(${getLocalizedVariantName(selectedVariant)})` : '' }} × {{ form.quantity }}</span>
                                <span class="font-bold text-gray-900">{{ formatPriceDisplay(unitPrice * form.quantity) }}</span>
                            </div>

                            <div v-if="cookingFeePerUnit > 0" class="flex justify-between text-gray-600">
                                <span>Biaya Olahan Matang × {{ form.quantity }}</span>
                                <span class="font-bold text-brand-700">+{{ formatPriceDisplay(cookingFeePerUnit * form.quantity) }}</span>
                            </div>

                            <div v-if="distributionFeePerUnit > 0" class="flex justify-between text-gray-600">
                                <span>Biaya Penyaluran × {{ form.quantity }}</span>
                                <span class="font-bold text-amber-700">+{{ formatPriceDisplay(distributionFeePerUnit * form.quantity) }}</span>
                            </div>

                            <div class="flex justify-between text-gray-600">
                                <span>Biaya Administrasi & Sertifikat</span>
                                <span class="font-bold text-emerald-600">Gratis (Rp 0)</span>
                            </div>

                            <div class="border-t border-gray-100 pt-3 flex justify-between items-baseline">
                                <span class="text-sm font-black text-gray-900">Total Pembayaran:</span>
                                <span class="text-xl font-black text-brand-600">{{ formatPriceDisplay(totalAmount) }}</span>
                            </div>
                        </div>

                        <!-- Payment Methods -->
                        <div class="mt-6 border-t border-gray-100 pt-4">
                            <InputLabel value="Pilih Metode Pembayaran *" class="!text-xs font-bold text-gray-900" />
                            <div class="mt-2 space-y-2">
                                <label
                                    v-for="pay in payment_options"
                                    :key="pay.value"
                                    class="flex items-center gap-2.5 rounded-xl border p-3 cursor-pointer text-xs transition"
                                    :class="form.payment_method === pay.value ? 'border-brand-500 bg-brand-50/50 font-bold text-brand-900' : 'border-gray-200 text-gray-700 hover:bg-gray-50'"
                                >
                                    <input
                                        type="radio"
                                        :value="pay.value"
                                        v-model="form.payment_method"
                                        class="text-brand-600 focus:ring-brand-500"
                                    />
                                    <span>{{ pay.label }}</span>
                                </label>
                            </div>
                            <InputError class="mt-1" :message="form.errors.payment_method" />
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-6">
                            <PrimaryButton
                                type="submit"
                                class="w-full !justify-center !rounded-2xl !py-3.5 !text-sm !font-black !bg-brand-600 hover:!bg-brand-700 shadow-lg shadow-brand-600/25"
                                :disabled="form.processing"
                            >
                                {{ form.processing ? 'Memproses Pesanan...' : 'Lanjut ke Pembayaran' }}
                            </PrimaryButton>
                            <p class="mt-2 text-center text-[11px] text-gray-400">
                                🔒 Transaksi dijamin aman dan terverifikasi syariat.
                            </p>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
