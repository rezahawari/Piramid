<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    service: Object,
    product: Object,
    distribution_options: Array,
    payment_options: Array,
});

const form = useForm({
    service_id: props.service.id,
    product_id: props.product.id,
    quantity: 1,
    distribution_type: 'pt_yayasan',
    recipient_name: '',
    recipient_phone: '',
    recipient_province: '',
    recipient_city: '',
    recipient_district: '',
    recipient_address: '',
    payment_method: 'midtrans',
});

const isMandiri = computed(() => form.distribution_type === 'alamat_mandiri');

const rupiah = (v) =>
    new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(v);

const total = computed(() => form.quantity * props.product.price);

const submit = () => form.post(route('checkout.store'));
</script>

<template>
    <Head :title="`Checkout ${product.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Checkout — {{ service.name }}
            </h2>
        </template>

        <div class="mx-auto max-w-3xl space-y-6 px-4 py-8 sm:px-6">
            <!-- Ringkasan produk -->
            <div class="flex items-center gap-4 rounded-lg bg-white p-4 shadow-sm">
                <img
                    v-if="product.primary_image_url"
                    :src="product.primary_image_url"
                    :alt="product.name"
                    class="h-20 w-20 rounded object-cover"
                />
                <div>
                    <h3 class="font-semibold text-gray-900">{{ product.name }}</h3>
                    <p class="text-sm text-gray-500">
                        {{ rupiah(product.price) }}
                        <span v-if="product.weight_estimate_kg"> · ±{{ product.weight_estimate_kg }} kg</span>
                        · Stok {{ product.stock }}
                    </p>
                </div>
            </div>

            <form class="space-y-6 rounded-lg bg-white p-6 shadow-sm" @submit.prevent="submit">
                <!-- Jumlah -->
                <div>
                    <InputLabel for="quantity" value="Jumlah" />
                    <TextInput
                        id="quantity"
                        v-model.number="form.quantity"
                        type="number"
                        min="1"
                        :max="product.stock"
                        class="mt-1 block w-32"
                        required
                    />
                    <InputError class="mt-2" :message="form.errors.quantity" />
                </div>

                <!-- Distribusi (USR-03) -->
                <fieldset>
                    <legend class="text-sm font-medium text-gray-700">Skema Penyaluran</legend>
                    <div class="mt-2 space-y-2">
                        <label
                            v-for="opt in distribution_options"
                            :key="opt.value"
                            class="flex items-center gap-2 rounded border p-3"
                            :class="form.distribution_type === opt.value ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200'"
                        >
                            <input v-model="form.distribution_type" type="radio" :value="opt.value" />
                            <span class="text-sm text-gray-800">{{ opt.label }}</span>
                        </label>
                    </div>
                    <InputError class="mt-2" :message="form.errors.distribution_type" />
                </fieldset>

                <!-- Alamat mandiri (kondisional) -->
                <div v-if="isMandiri" class="grid gap-4 rounded-lg border border-gray-200 p-4 sm:grid-cols-2">
                    <div>
                        <InputLabel for="recipient_name" value="Nama Penerima" />
                        <TextInput id="recipient_name" v-model="form.recipient_name" class="mt-1 block w-full" required />
                        <InputError class="mt-2" :message="form.errors.recipient_name" />
                    </div>
                    <div>
                        <InputLabel for="recipient_phone" value="Telepon" />
                        <TextInput id="recipient_phone" v-model="form.recipient_phone" class="mt-1 block w-full" required />
                        <InputError class="mt-2" :message="form.errors.recipient_phone" />
                    </div>
                    <div>
                        <InputLabel for="recipient_province" value="Provinsi" />
                        <TextInput id="recipient_province" v-model="form.recipient_province" class="mt-1 block w-full" required />
                        <InputError class="mt-2" :message="form.errors.recipient_province" />
                    </div>
                    <div>
                        <InputLabel for="recipient_city" value="Kota/Kabupaten" />
                        <TextInput id="recipient_city" v-model="form.recipient_city" class="mt-1 block w-full" required />
                        <InputError class="mt-2" :message="form.errors.recipient_city" />
                    </div>
                    <div>
                        <InputLabel for="recipient_district" value="Kecamatan" />
                        <TextInput id="recipient_district" v-model="form.recipient_district" class="mt-1 block w-full" required />
                        <InputError class="mt-2" :message="form.errors.recipient_district" />
                    </div>
                    <div class="sm:col-span-2">
                        <InputLabel for="recipient_address" value="Alamat Lengkap" />
                        <textarea
                            id="recipient_address"
                            v-model="form.recipient_address"
                            rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            required
                        />
                        <InputError class="mt-2" :message="form.errors.recipient_address" />
                    </div>
                </div>

                <!-- Metode pembayaran (USR-04) -->
                <fieldset>
                    <legend class="text-sm font-medium text-gray-700">Metode Pembayaran</legend>
                    <div class="mt-2 space-y-2">
                        <label
                            v-for="opt in payment_options"
                            :key="opt.value"
                            class="flex items-center gap-2 rounded border p-3"
                            :class="form.payment_method === opt.value ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200'"
                        >
                            <input v-model="form.payment_method" type="radio" :value="opt.value" />
                            <span class="text-sm text-gray-800">{{ opt.label }}</span>
                        </label>
                    </div>
                    <InputError class="mt-2" :message="form.errors.payment_method" />
                </fieldset>

                <div class="flex items-center justify-between border-t pt-4">
                    <div>
                        <p class="text-sm text-gray-500">Total</p>
                        <p class="text-lg font-bold text-gray-900">{{ rupiah(total) }}</p>
                    </div>
                    <PrimaryButton :disabled="form.processing">Buat Pesanan</PrimaryButton>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
