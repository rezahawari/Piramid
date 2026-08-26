<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

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

const form = useForm({
    service_id: props.service.id,
    product_id: props.product.id,
    quantity: 1,
    distribution_type: 'pt_yayasan',
    distribution_location_note: '',
    recipient_name: user.value?.name ?? '',
    recipient_phone: '',
    recipient_province: '',
    recipient_city: '',
    recipient_district: '',
    recipient_address: '',
    sohibul_names: [user.value?.name ?? ''],
    payment_method: 'midtrans',
});

const isMandiri = computed(() => form.distribution_type === 'alamat_mandiri');
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

const rupiah = (v) =>
    new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(Number(v || 0));

const subtotal = computed(() => form.quantity * Number(props.product.price));
const serviceFee = 0; // Biaya admin gratis
const total = computed(() => subtotal.value + serviceFee);

const serviceIcons = {
    qurban: '/images/service-icons/sapi.jpg',
    aqiqah: '/images/service-icons/kambing.jpg',
    sedekah: '/images/service-icons/domba.jpg',
};

const submit = () => form.post(route('checkout.store'));
</script>

<template>
    <Head :title="`Konfirmasi Pemesanan - ${product.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-bold leading-tight text-gray-900">Formulir Pemesanan</h2>
                    <p class="text-xs text-gray-500">
                        Lengkapi rincian pesanan ibadah {{ service.name }} Anda dengan aman.
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

        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <form @submit.prevent="submit">
                <div class="grid grid-cols-1 gap-8 lg:grid-cols-12 lg:items-start">
                    
                    <!-- LEFT COLUMN (7 Cols): Formulir Detail Pemesanan -->
                    <div class="space-y-6 lg:col-span-7">
                        
                        <!-- 1. Pilihan Jumlah Hewan -->
                        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                            <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-7 w-7 items-center justify-center rounded-full bg-brand-50 text-xs font-bold text-brand-600">
                                        1
                                    </span>
                                    <h3 class="text-base font-bold text-gray-900">Jumlah Hewan</h3>
                                </div>
                                <span class="text-xs text-gray-500">
                                    Tersedia: <strong class="text-emerald-600">{{ product.stock }} ekor</strong>
                                </span>
                            </div>

                            <div class="mt-5 flex items-center gap-4">
                                <div class="flex items-center rounded-xl border border-gray-200 bg-gray-50 p-1">
                                    <button
                                        type="button"
                                        @click="form.quantity > 1 ? form.quantity-- : null"
                                        class="flex h-9 w-9 items-center justify-center rounded-lg bg-white text-gray-700 shadow-sm transition hover:bg-gray-100 disabled:opacity-40"
                                        :disabled="form.quantity <= 1"
                                    >
                                        -
                                    </button>
                                    <input
                                        v-model.number="form.quantity"
                                        type="number"
                                        min="1"
                                        :max="product.stock"
                                        class="w-16 border-0 bg-transparent text-center text-sm font-bold text-gray-900 focus:ring-0"
                                        required
                                    />
                                    <button
                                        type="button"
                                        @click="form.quantity < product.stock ? form.quantity++ : null"
                                        class="flex h-9 w-9 items-center justify-center rounded-lg bg-white text-gray-700 shadow-sm transition hover:bg-gray-100 disabled:opacity-40"
                                        :disabled="form.quantity >= product.stock"
                                    >
                                        +
                                    </button>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Harga Satuan:</p>
                                    <p class="text-sm font-bold text-brand-600">{{ rupiah(product.price) }} / ekor</p>
                                </div>
                            </div>
                            <InputError class="mt-2" :message="form.errors.quantity" />
                        </div>

                        <!-- 2. Data Sohibul Qurban/Aqiqah (Kondisional jika Layanan mengaktifkan has_sohibul) -->
                        <div v-if="service.has_sohibul" class="rounded-2xl border-2 border-brand-500/40 bg-white p-6 shadow-sm">
                            <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-7 w-7 items-center justify-center rounded-full bg-brand-500 text-xs font-bold text-white shadow-xs">
                                        {{ service.has_sohibul ? '2' : '•' }}
                                    </span>
                                    <div>
                                        <h3 class="text-base font-bold text-gray-900">Nama Sohibul (Atas Nama Ibadah)</h3>
                                        <p class="text-xs text-gray-500">
                                            Cantumkan nama-nama yang diniatkan untuk ibadah {{ service.name }}.
                                        </p>
                                    </div>
                                </div>
                                <span class="rounded-lg bg-brand-50 border border-brand-200 px-2.5 py-1 text-xs font-bold text-brand-700">
                                    Maks. {{ maxSohibulTotal }} Orang ({{ form.quantity }} &times; {{ product.max_sohibul || 1 }})
                                </span>
                            </div>

                            <div class="mt-5 space-y-3">
                                <div
                                    v-for="(name, index) in form.sohibul_names"
                                    :key="index"
                                    class="flex items-center gap-2"
                                >
                                    <div class="relative flex-1">
                                        <span class="absolute left-3.5 top-2.5 text-xs font-bold text-gray-400">
                                            #{{ index + 1 }}
                                        </span>
                                        <TextInput
                                            v-model="form.sohibul_names[index]"
                                            type="text"
                                            class="block w-full !rounded-xl !pl-10 !text-xs"
                                            :placeholder="`Nama Sohibul ${index + 1} (Contoh: ${index === 0 ? (user?.name || 'Ahmad bin Abdullah') : 'Fatimah binti Ahmad'})`"
                                            required
                                        />
                                    </div>
                                    <button
                                        v-if="form.sohibul_names.length > 1"
                                        type="button"
                                        @click="removeSohibul(index)"
                                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-rose-50 text-rose-600 transition hover:bg-rose-100"
                                        title="Hapus nama ini"
                                    >
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>

                                <div class="pt-2 flex items-center justify-between">
                                    <button
                                        v-if="form.sohibul_names.length < maxSohibulTotal"
                                        type="button"
                                        @click="addSohibul"
                                        class="inline-flex items-center gap-1.5 rounded-xl border border-dashed border-brand-400 bg-brand-50/50 px-4 py-2 text-xs font-bold text-brand-700 transition hover:bg-brand-100"
                                    >
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Tambah Nama Sohibul ({{ form.sohibul_names.length }}/{{ maxSohibulTotal }})
                                    </button>
                                    <span v-else class="text-xs text-emerald-600 font-semibold">
                                        ✓ Kuota maksimal {{ maxSohibulTotal }} nama telah terpenuhi
                                    </span>
                                </div>
                            </div>
                            <InputError class="mt-2" :message="form.errors.sohibul_names" />
                        </div>

                        <!-- 3. Skema Penyaluran & Distribusi -->
                        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                            <div class="flex items-center gap-3 border-b border-gray-100 pb-4">
                                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-brand-50 text-xs font-bold text-brand-600">
                                    {{ service.has_sohibul ? '3' : '2' }}
                                </span>
                                <div>
                                    <h3 class="text-base font-bold text-gray-900">Skema Penyaluran Daging</h3>
                                    <p class="text-xs text-gray-500">Pilih bagaimana daging qurban/aqiqah akan didistribusikan.</p>
                                </div>
                            </div>

                            <div class="mt-4 space-y-3">
                                <label
                                    v-for="opt in distribution_options"
                                    :key="opt.value"
                                    class="relative flex cursor-pointer items-start gap-3.5 rounded-xl border p-4 transition duration-150"
                                    :class="form.distribution_type === opt.value ? 'border-brand-500 bg-brand-50/40 ring-1 ring-brand-500' : 'border-gray-200 hover:bg-gray-50/60'"
                                >
                                    <input
                                        v-model="form.distribution_type"
                                        type="radio"
                                        :value="opt.value"
                                        class="mt-1 text-brand-600 focus:ring-brand-500"
                                    />
                                    <div class="flex-1">
                                        <span class="block text-sm font-bold text-gray-900">{{ opt.label }}</span>
                                        <p class="mt-0.5 text-xs text-gray-500">
                                            {{
                                                opt.value === 'pt_yayasan'
                                                    ? 'Daging disalurkan secara amanah kepada kaum dhuafa, santri, dan masyarakat pelosok melalui jaringan Piramid.'
                                                    : 'Daging hasil olahan/potong diantar langsung ke alamat tujuan penerima yang Anda tentukan.'
                                            }}
                                        </p>
                                    </div>
                                </label>
                            </div>
                            <InputError class="mt-2" :message="form.errors.distribution_type" />

                            <!-- Form Alamat Pengiriman (Kondisional Mandiri) -->
                            <div v-if="isMandiri" class="mt-6 rounded-xl border border-brand-200 bg-white p-4 space-y-4">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-brand-800">
                                    Data Penerima & Alamat Pengiriman
                                </h4>
                                
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <div>
                                        <InputLabel for="recipient_name" value="Nama Penerima" />
                                        <TextInput
                                            id="recipient_name"
                                            v-model="form.recipient_name"
                                            class="mt-1 block w-full text-sm"
                                            placeholder="Nama lengkap"
                                            required
                                        />
                                        <InputError class="mt-1" :message="form.errors.recipient_name" />
                                    </div>

                                    <div>
                                        <InputLabel for="recipient_phone" value="No. WhatsApp / Telepon" />
                                        <TextInput
                                            id="recipient_phone"
                                            v-model="form.recipient_phone"
                                            type="tel"
                                            class="mt-1 block w-full text-sm"
                                            placeholder="0812xxxxxxxx"
                                            required
                                        />
                                        <InputError class="mt-1" :message="form.errors.recipient_phone" />
                                    </div>

                                    <div>
                                        <InputLabel for="recipient_province" value="Provinsi" />
                                        <TextInput
                                            id="recipient_province"
                                            v-model="form.recipient_province"
                                            class="mt-1 block w-full text-sm"
                                            placeholder="Contoh: Jawa Barat"
                                            required
                                        />
                                        <InputError class="mt-1" :message="form.errors.recipient_province" />
                                    </div>

                                    <div>
                                        <InputLabel for="recipient_city" value="Kota / Kabupaten" />
                                        <TextInput
                                            id="recipient_city"
                                            v-model="form.recipient_city"
                                            class="mt-1 block w-full text-sm"
                                            placeholder="Contoh: Bandung"
                                            required
                                        />
                                        <InputError class="mt-1" :message="form.errors.recipient_city" />
                                    </div>

                                    <div class="sm:col-span-2">
                                        <InputLabel for="recipient_district" value="Kecamatan" />
                                        <TextInput
                                            id="recipient_district"
                                            v-model="form.recipient_district"
                                            class="mt-1 block w-full text-sm"
                                            placeholder="Contoh: Coblong"
                                            required
                                        />
                                        <InputError class="mt-1" :message="form.errors.recipient_district" />
                                    </div>

                                    <div class="sm:col-span-2">
                                        <InputLabel for="recipient_address" value="Alamat Lengkap & Patokan" />
                                        <textarea
                                            id="recipient_address"
                                            v-model="form.recipient_address"
                                            rows="2"
                                            class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500"
                                            placeholder="Jl. Mawar No. 123, RT 01/RW 02..."
                                            required
                                        ></textarea>
                                        <InputError class="mt-1" :message="form.errors.recipient_address" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 4. Metode Pembayaran -->
                        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                            <div class="flex items-center gap-3 border-b border-gray-100 pb-4">
                                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-brand-50 text-xs font-bold text-brand-600">
                                    {{ service.has_sohibul ? '4' : '3' }}
                                </span>
                                <div>
                                    <h3 class="text-base font-bold text-gray-900">Metode Pembayaran</h3>
                                    <p class="text-xs text-gray-500">Pilih saluran pembayaran yang Anda kehendaki.</p>
                                </div>
                            </div>

                            <div class="mt-4 space-y-3">
                                <label
                                    v-for="opt in payment_options"
                                    :key="opt.value"
                                    class="relative flex cursor-pointer items-center justify-between rounded-xl border p-4 transition duration-150"
                                    :class="form.payment_method === opt.value ? 'border-brand-500 bg-brand-50/40 ring-1 ring-brand-500' : 'border-gray-200 hover:bg-gray-50/60'"
                                >
                                    <div class="flex items-center gap-3">
                                        <input
                                            v-model="form.payment_method"
                                            type="radio"
                                            :value="opt.value"
                                            class="text-brand-600 focus:ring-brand-500"
                                        />
                                        <div>
                                            <span class="block text-sm font-bold text-gray-900">{{ opt.label }}</span>
                                            <span class="text-xs text-gray-500">
                                                {{ opt.value === 'midtrans' ? 'Virtual Account (BCA, Mandiri, BNI, BRI), QRIS, Gopay' : 'Transfer bank langsung & upload bukti transfer manual' }}
                                            </span>
                                        </div>
                                    </div>
                                    <span
                                        class="rounded-md px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider"
                                        :class="opt.value === 'midtrans' ? 'bg-blue-50 text-blue-700' : 'bg-amber-50 text-amber-700'"
                                    >
                                        {{ opt.value === 'midtrans' ? 'Otomatis' : 'Manual' }}
                                    </span>
                                </label>
                            </div>
                            <InputError class="mt-2" :message="form.errors.payment_method" />
                        </div>
                    </div>

                    <!-- RIGHT COLUMN (5 Cols): Rincian Detail Layanan & Hewan + Ringkasan Biaya -->
                    <div class="space-y-6 lg:col-span-5">
                        
                        <!-- Rincian Layanan & Hewan Terpilih -->
                        <div class="overflow-hidden rounded-2xl border border-gray-200/80 bg-white shadow-sm">
                            <!-- Header Layanan -->
                            <div class="relative bg-zinc-900 p-5 text-white">
                                <img
                                    v-if="service.cover_image_url"
                                    :src="service.cover_image_url"
                                    :alt="service.name"
                                    class="absolute inset-0 h-full w-full object-cover opacity-20"
                                />
                                <img
                                    v-else-if="serviceIcons[service.slug]"
                                    :src="serviceIcons[service.slug]"
                                    :alt="service.name"
                                    class="absolute inset-0 h-full w-full object-cover opacity-20"
                                />
                                <div class="relative z-10">
                                    <span class="rounded bg-brand-500/90 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-white">
                                        Layanan Terpilih
                                    </span>
                                    <h4 class="mt-1.5 text-lg font-bold text-white">{{ service.name }}</h4>
                                    <p class="mt-1 text-xs text-zinc-300 line-clamp-2">
                                        {{ service.description || 'Layanan ibadah terbaik dari Piramid.' }}
                                    </p>
                                </div>
                            </div>

                            <!-- Detail Produk Hewan -->
                            <div class="p-5">
                                <h5 class="text-xs font-bold uppercase tracking-wider text-gray-400">Hewan yang Dipilih</h5>
                                <div class="mt-3 flex gap-4">
                                    <img
                                        v-if="product.primary_image_url"
                                        :src="product.primary_image_url"
                                        :alt="product.name"
                                        class="h-20 w-20 shrink-0 rounded-xl border border-gray-100 object-cover"
                                    />
                                    <div v-else class="flex h-20 w-20 shrink-0 items-center justify-center rounded-xl bg-gray-100 text-xs text-gray-400">
                                        Foto
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-base font-bold text-gray-900">{{ product.name }}</h4>
                                        <p class="mt-0.5 text-xs text-gray-500 line-clamp-2">
                                            {{ product.description || 'Hewan sehat siap qurban & aqiqah sesuai syariat.' }}
                                        </p>
                                        <div class="mt-2 flex items-center gap-2 text-xs">
                                            <span v-if="product.weight_estimate_kg" class="rounded bg-gray-100 px-2 py-0.5 font-medium text-gray-700">
                                                ~{{ product.weight_estimate_kg }} Kg
                                            </span>
                                            <span class="rounded bg-emerald-50 px-2 py-0.5 font-semibold text-emerald-700">
                                                Siap Potong
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Garansi & Fitur Layanan -->
                                <div class="mt-5 rounded-xl bg-gray-50 p-3.5 space-y-2 text-xs text-gray-600">
                                    <div class="flex items-center gap-2">
                                        <svg class="h-4 w-4 text-brand-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span>Penyembelihan syari bersertifikat halal</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <svg class="h-4 w-4 text-brand-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span>Dokumentasi video & foto proses</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <svg class="h-4 w-4 text-brand-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span>Sertifikat pelaporan resmi</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Ringkasan Biaya & Tombol Submit -->
                        <div class="rounded-2xl border border-gray-200/80 bg-white p-6 shadow-sm">
                            <h4 class="text-base font-bold text-gray-900">Ringkasan Pembayaran</h4>

                            <dl class="mt-4 space-y-3 text-xs text-gray-600">
                                <div class="flex justify-between">
                                    <dt>{{ product.name }} (x{{ form.quantity }})</dt>
                                    <dd class="font-medium text-gray-900">{{ rupiah(subtotal) }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt>Biaya Operasional & Pemotongan</dt>
                                    <dd class="font-medium text-emerald-600">Sudah Termasuk</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt>Biaya Layanan Sistem</dt>
                                    <dd class="font-medium text-emerald-600">Gratis</dd>
                                </div>
                                <div class="flex justify-between border-t border-gray-100 pt-3 text-sm">
                                    <dt class="font-bold text-gray-900">Total Tagihan</dt>
                                    <dd class="text-lg font-black text-brand-600">{{ rupiah(total) }}</dd>
                                </div>
                            </dl>

                            <div class="mt-6">
                                <PrimaryButton
                                    class="w-full justify-center !rounded-xl !py-3.5 !text-sm font-bold shadow-md"
                                    :disabled="form.processing || product.stock < 1"
                                >
                                    {{ form.processing ? 'Memproses Pesanan...' : 'Konfirmasi & Lanjutkan Pembayaran' }}
                                </PrimaryButton>
                            </div>

                            <p class="mt-3 text-center text-[11px] text-gray-400">
                                Transaksi aman & terenkripsi. Anda dapat membatalkan pesanan sebelum pembayaran terkonfirmasi.
                            </p>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
