<script setup>
import PublicFooter from '@/Components/PublicFooter.vue';
import PublicNavbar from '@/Components/PublicNavbar.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    services: {
        type: Array,
        default: () => [],
    },
});

const selectedService = ref(null);
const isModalOpen = ref(false);

const openServiceModal = (svc) => {
    selectedService.value = svc;
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
};

const rupiah = (value) =>
    new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(Number(value ?? 0));

const serviceIcons = {
    qurban: '/images/service-icons/sapi.jpg',
    aqiqah: '/images/service-icons/kambing.jpg',
    sedekah: '/images/service-icons/domba.jpg',
};
</script>

<template>
    <Head title="Pilihan Layanan Pemesanan" />

    <div class="min-h-screen bg-brand-50 font-sans">
        <PublicNavbar />

        <!-- Header Section -->
        <header class="border-b border-zinc-200/60 bg-white">
            <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8 text-center">
                <span class="inline-flex items-center rounded-full bg-brand-50 px-3 py-1 text-xs font-semibold text-brand-700 ring-1 ring-inset ring-brand-700/10">
                    Katalog & Pemesanan
                </span>
                <h1 class="mt-3 text-3xl font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                    Pilih Layanan Ibadah Anda
                </h1>
                <p class="mx-auto mt-3 max-w-2xl text-base text-gray-600">
                    Tunaikan ibadah Qurban, Aqiqah, atau Sedekah dengan mudah, transparan, dan sesuai syariat. Klik layanan untuk melihat detail dan daftar hewan yang tersedia.
                </p>
            </div>
        </header>

        <!-- Services Grid -->
        <main class="py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div v-if="services.length" class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="svc in services"
                        :key="svc.id"
                        class="group flex flex-col overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl"
                    >
                        <!-- Cover Image -->
                        <div class="relative h-52 w-full overflow-hidden bg-gray-100 cursor-pointer" @click="openServiceModal(svc)">
                            <img
                                v-if="svc.cover_image_url"
                                :src="svc.cover_image_url"
                                :alt="svc.name"
                                class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                            />
                            <img
                                v-else-if="serviceIcons[svc.slug]"
                                :src="serviceIcons[svc.slug]"
                                :alt="svc.name"
                                class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                            />
                            <div v-else class="flex h-full w-full items-center justify-center bg-gray-200 text-gray-400">
                                Tidak ada foto
                            </div>
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                            <div class="absolute bottom-3 left-4 right-4 text-white">
                                <span class="rounded-full bg-brand-500/90 px-2.5 py-0.5 text-xs font-semibold backdrop-blur">
                                    {{ svc.products?.length || 0 }} Pilihan Hewan
                                </span>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="flex flex-1 flex-col p-6">
                            <h2 class="text-xl font-bold text-gray-900 group-hover:text-brand-600 transition">
                                {{ svc.name }}
                            </h2>
                            <p class="mt-2 line-clamp-3 text-sm text-gray-600">
                                {{ svc.description || 'Layanan ibadah terbaik dari Piramid.' }}
                            </p>

                            <!-- Mini Animals Preview Chips -->
                            <div v-if="svc.products?.length" class="mt-4">
                                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Hewan Tersedia:</p>
                                <div class="mt-2 flex flex-wrap gap-1.5">
                                    <span
                                        v-for="p in svc.products.slice(0, 3)"
                                        :key="p.id"
                                        class="inline-flex items-center rounded-md bg-zinc-100 px-2 py-1 text-xs font-medium text-zinc-700"
                                    >
                                        {{ p.name }}
                                    </span>
                                    <span
                                        v-if="svc.products.length > 3"
                                        class="inline-flex items-center rounded-md bg-brand-50 px-2 py-1 text-xs font-medium text-brand-700"
                                    >
                                        +{{ svc.products.length - 3 }} lainnya
                                    </span>
                                </div>
                            </div>

                            <!-- Actions Button -->
                            <div class="mt-6 pt-4 border-t border-gray-100 flex items-center gap-3">
                                <button
                                    type="button"
                                    @click="openServiceModal(svc)"
                                    class="flex-1 rounded-xl bg-brand-500 px-4 py-2.5 text-center text-sm font-bold text-white shadow-sm transition hover:bg-brand-600"
                                >
                                    Lihat Detail & Hewan
                                </button>
                                <Link
                                    :href="route('catalog.index', svc.slug)"
                                    class="rounded-xl border border-gray-200 px-3.5 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
                                    title="Halaman Penuh"
                                >
                                    &rarr;
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else class="rounded-2xl border border-dashed border-gray-300 bg-white p-12 text-center shadow-sm">
                    <p class="text-base text-gray-500">Belum ada layanan aktif yang tersedia saat ini.</p>
                </div>
            </div>
        </main>

        <!-- Detail Service & Animals Modal Popup -->
        <div
            v-if="isModalOpen && selectedService"
            class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black/60 p-4 backdrop-blur-sm transition-opacity"
            @click.self="closeModal"
        >
            <div class="relative w-full max-w-3xl overflow-hidden rounded-2xl bg-white shadow-2xl transition-all">
                <!-- Modal Header with Cover -->
                <div class="relative h-48 sm:h-56 w-full overflow-hidden bg-zinc-900">
                    <img
                        v-if="selectedService.cover_image_url"
                        :src="selectedService.cover_image_url"
                        :alt="selectedService.name"
                        class="h-full w-full object-cover opacity-80"
                    />
                    <img
                        v-else-if="serviceIcons[selectedService.slug]"
                        :src="serviceIcons[selectedService.slug]"
                        :alt="selectedService.name"
                        class="h-full w-full object-cover opacity-80"
                    />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent"></div>

                    <!-- Close Button -->
                    <button
                        type="button"
                        @click="closeModal"
                        class="absolute right-4 top-4 flex h-9 w-9 items-center justify-center rounded-full bg-black/40 text-white backdrop-blur transition hover:bg-black/70"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <!-- Title & Badge -->
                    <div class="absolute bottom-4 left-6 right-6 text-white">
                        <span class="rounded-md bg-brand-500 px-2.5 py-1 text-xs font-bold uppercase tracking-wider">
                            Layanan Ibadah
                        </span>
                        <h3 class="mt-2 text-2xl sm:text-3xl font-extrabold tracking-tight">
                            {{ selectedService.name }}
                        </h3>
                    </div>
                </div>

                <!-- Modal Body Content -->
                <div class="max-h-[60vh] overflow-y-auto p-6 space-y-6">
                    <!-- Description -->
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-gray-400">Deskripsi Layanan</h4>
                        <p class="mt-2 text-sm text-gray-700 leading-relaxed">
                            {{ selectedService.description || 'Tidak ada deskripsi rinci untuk layanan ini.' }}
                        </p>
                    </div>

                    <!-- Available Animals / Products List -->
                    <div>
                        <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-gray-400">
                                Pilihan Hewan Tersedia ({{ selectedService.products?.length || 0 }})
                            </h4>
                            <span class="text-xs text-brand-600 font-semibold">Pilih hewan untuk mulai pesan</span>
                        </div>

                        <div v-if="selectedService.products?.length" class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div
                                v-for="product in selectedService.products"
                                :key="product.id"
                                class="flex flex-col justify-between rounded-xl border border-gray-200 bg-zinc-50/50 p-4 transition hover:border-brand-500 hover:bg-white hover:shadow-md"
                            >
                                <div class="flex gap-3">
                                    <img
                                        v-if="product.primary_image_url"
                                        :src="product.primary_image_url"
                                        :alt="product.name"
                                        class="h-16 w-16 shrink-0 rounded-lg object-cover border border-gray-200"
                                    />
                                    <div v-else class="flex h-16 w-16 shrink-0 items-center justify-center rounded-lg bg-gray-200 text-[10px] text-gray-400">
                                        Foto
                                    </div>
                                    <div>
                                        <h5 class="text-sm font-bold text-gray-900">{{ product.name }}</h5>
                                        <p class="text-xs font-extrabold text-brand-600 mt-0.5">
                                            {{ rupiah(product.price) }}
                                        </p>
                                        <p v-if="product.weight_estimate_kg" class="text-[11px] text-gray-500">
                                            Bobot: ~{{ product.weight_estimate_kg }} kg
                                        </p>
                                    </div>
                                </div>

                                <div class="mt-3 pt-3 border-t border-gray-200/60 flex items-center justify-between">
                                    <span
                                        class="text-xs font-medium"
                                        :class="product.stock > 0 ? 'text-emerald-600' : 'text-rose-600'"
                                    >
                                        {{ product.stock > 0 ? `Sisa Stok: ${product.stock}` : 'Habis' }}
                                    </span>
                                    <Link
                                        :href="route('catalog.show', { service: selectedService.slug, product: product.slug })"
                                        class="rounded-lg bg-brand-500 px-3 py-1.5 text-xs font-bold text-white shadow-sm transition hover:bg-brand-600"
                                    >
                                        Pesan Hewan &rarr;
                                    </Link>
                                </div>
                            </div>
                        </div>

                        <div v-else class="mt-4 rounded-xl bg-gray-50 p-6 text-center text-xs text-gray-500">
                            Saat ini belum ada hewan yang ditautkan ke layanan ini.
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="border-t border-gray-100 bg-gray-50 px-6 py-4 flex items-center justify-between">
                    <Link
                        :href="route('catalog.index', selectedService.slug)"
                        class="text-xs font-semibold text-brand-600 hover:text-brand-700 hover:underline"
                    >
                        Buka Halaman Lengkap Layanan Ini &rarr;
                    </Link>
                    <button
                        type="button"
                        @click="closeModal"
                        class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-xs font-semibold text-gray-700 shadow-sm hover:bg-gray-100"
                    >
                        Tutup
                    </button>
                </div>
            </div>
        </div>

        <PublicFooter />
    </div>
</template>
