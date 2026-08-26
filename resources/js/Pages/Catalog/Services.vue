<script setup>
import PublicFooter from '@/Components/PublicFooter.vue';
import PublicNavbar from '@/Components/PublicNavbar.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    services: {
        type: Array,
        default: () => [],
    },
});

const page = usePage();
const user = computed(() => page.props.auth?.user);

const selectedService = ref(null);
const isModalOpen = ref(false);
const searchQuery = ref('');

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

const filteredServices = computed(() => {
    if (!searchQuery.value.trim()) return props.services;
    const q = searchQuery.value.toLowerCase();
    return props.services.filter(
        (s) => s.name.toLowerCase().includes(q) || (s.description && s.description.toLowerCase().includes(q))
    );
});
</script>

<template>
    <Head title="Pilihan Layanan Pemesanan" />

    <div class="min-h-screen bg-slate-50 font-sans pb-32 md:pb-0">
        <PublicNavbar />

        <!-- ================= MOBILE APP NATIVE HEADER (Tampil Khusus di Layar HP) ================= -->
        <div class="block md:hidden bg-gradient-to-b from-slate-900 via-brand-950 to-brand-900 text-white pt-5 pb-8 px-5 rounded-b-[2rem] shadow-xl relative overflow-hidden">
            <!-- Background Glow -->
            <div class="absolute -right-16 -top-16 w-48 h-48 bg-amber-400/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -left-16 bottom-0 w-48 h-48 bg-brand-600/30 rounded-full blur-3xl pointer-events-none"></div>

            <!-- Top Bar: Avatar & Brand Greeting -->
            <div class="flex items-center justify-between relative z-10">
                <div class="flex items-center gap-3">
                    <div class="h-11 w-11 rounded-full bg-gradient-to-tr from-brand-600 to-amber-400 p-0.5 shadow-md flex items-center justify-center">
                        <div class="h-full w-full rounded-full bg-slate-900 flex items-center justify-center text-amber-300 font-black text-sm">
                            {{ user ? user.name.charAt(0).toUpperCase() : 'P' }}
                        </div>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold text-zinc-400">
                            {{ user ? `Assalamu'alaikum,` : 'Selamat Datang di' }}
                        </p>
                        <h2 class="text-base font-extrabold text-white tracking-tight truncate max-w-[190px]">
                            {{ user ? user.name : 'Piramid Qurban' }}
                        </h2>
                    </div>
                </div>

                <!-- Status Badge App -->
                <div class="flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md border border-white/15 text-[11px] font-bold text-amber-300 shadow-sm">
                    <span class="flex h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Syari & Amanah
                </div>
            </div>

            <!-- Native Search Input Field -->
            <div class="mt-5 relative z-10">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Cari jenis layanan atau qurban..."
                        class="w-full bg-white/10 backdrop-blur-md border border-white/15 rounded-2xl pl-10 pr-4 py-2.5 text-xs text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-amber-400/50 focus:border-amber-400/50 transition shadow-inner"
                    />
                    <button
                        v-if="searchQuery"
                        @click="searchQuery = ''"
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-zinc-400 hover:text-white"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mini Story Chips: Jaminan Mutu -->
            <div class="mt-4 flex items-center gap-2 overflow-x-auto no-scrollbar pt-1 relative z-10">
                <span class="shrink-0 px-2.5 py-1 rounded-xl bg-white/10 border border-white/10 text-[10px] font-semibold text-zinc-200 flex items-center gap-1">
                    <span>✨</span> Potong di RPH
                </span>
                <span class="shrink-0 px-2.5 py-1 rounded-xl bg-white/10 border border-white/10 text-[10px] font-semibold text-zinc-200 flex items-center gap-1">
                    <span>📹</span> Video & Foto
                </span>
                <span class="shrink-0 px-2.5 py-1 rounded-xl bg-white/10 border border-white/10 text-[10px] font-semibold text-zinc-200 flex items-center gap-1">
                    <span>📜</span> Sertifikat Resmi
                </span>
            </div>
        </div>

        <!-- ================= DESKTOP HEADER (Tampil Khusus di Layar Besar) ================= -->
        <header class="hidden md:block border-b border-zinc-200/60 bg-white">
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

        <!-- ================= MAIN CONTENT: SERVICES LIST / CARDS ================= -->
        <main class="py-6 md:py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                
                <!-- Section Title for Mobile -->
                <div class="flex items-center justify-between mb-4 md:hidden">
                    <div>
                        <h3 class="text-sm font-extrabold text-gray-900 uppercase tracking-wider">Pilihan Layanan</h3>
                        <p class="text-[11px] text-gray-500">Pilih paket ibadah yang Anda inginkan</p>
                    </div>
                    <span class="text-[11px] font-bold text-brand-600 bg-brand-50 px-2 py-0.5 rounded-lg">
                        {{ filteredServices.length }} Layanan
                    </span>
                </div>

                <!-- Grid / List of Services -->
                <div v-if="filteredServices.length" class="grid grid-cols-1 gap-4 sm:gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="svc in filteredServices"
                        :key="svc.id"
                        class="group flex flex-col overflow-hidden rounded-2xl md:rounded-3xl border border-gray-200/80 bg-white shadow-sm hover:shadow-xl transition-all duration-300 active:scale-[0.99] md:hover:-translate-y-1.5"
                    >
                        <!-- Cover Image with Native Card Overlay -->
                        <div class="relative h-44 sm:h-52 w-full overflow-hidden bg-gray-900 cursor-pointer" @click="openServiceModal(svc)">
                            <img
                                v-if="svc.cover_image_url"
                                :src="svc.cover_image_url"
                                :alt="svc.name"
                                class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105"
                            />
                            <img
                                v-else-if="serviceIcons[svc.slug]"
                                :src="serviceIcons[svc.slug]"
                                :alt="svc.name"
                                class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105"
                            />
                            <div v-else class="flex h-full w-full items-center justify-center bg-gray-200 text-gray-400">
                                Tidak ada foto
                            </div>
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                            
                            <!-- Badges Overlay -->
                            <div class="absolute top-3 left-3 flex items-center gap-1.5">
                                <span
                                    v-if="svc.has_sohibul"
                                    class="rounded-lg bg-amber-500/90 backdrop-blur-md px-2 py-0.5 text-[10px] font-bold text-white shadow-sm uppercase tracking-wider"
                                >
                                    Sohibul
                                </span>
                            </div>

                            <div class="absolute bottom-3 left-3.5 right-3.5 flex items-center justify-between text-white">
                                <span class="rounded-full bg-white/20 backdrop-blur-md border border-white/20 px-2.5 py-0.5 text-[11px] font-bold text-white shadow-xs">
                                    {{ svc.products?.length || 0 }} Pilihan Hewan
                                </span>
                                <span class="text-[10px] text-amber-300 font-semibold flex items-center gap-1">
                                    <span>Lihat Hewan</span>
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                                    </svg>
                                </span>
                            </div>
                        </div>

                        <!-- Card Content Body (Native App Tile Style) -->
                        <div class="flex flex-1 flex-col p-4 sm:p-6">
                            <div class="flex items-start justify-between gap-2">
                                <h2 class="text-lg sm:text-xl font-black text-gray-900 group-hover:text-brand-600 transition tracking-tight">
                                    {{ svc.name }}
                                </h2>
                                <span class="shrink-0 p-1.5 rounded-xl bg-brand-50 text-brand-600 group-hover:bg-brand-600 group-hover:text-white transition">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </span>
                            </div>

                            <p class="mt-1.5 line-clamp-2 text-xs text-gray-500 leading-relaxed">
                                {{ svc.description || 'Layanan ibadah terbaik dari Piramid.' }}
                            </p>

                            <!-- Mini Animals Chips Preview -->
                            <div v-if="svc.products?.length" class="mt-3.5 pt-3 border-t border-gray-100">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span
                                        v-for="p in svc.products.slice(0, 3)"
                                        :key="p.id"
                                        class="inline-flex items-center rounded-lg bg-gray-100/80 px-2 py-0.5 text-[11px] font-semibold text-gray-700"
                                    >
                                        {{ p.name }}
                                    </span>
                                    <span
                                        v-if="svc.products.length > 3"
                                        class="inline-flex items-center rounded-lg bg-brand-50 px-2 py-0.5 text-[11px] font-bold text-brand-700"
                                    >
                                        +{{ svc.products.length - 3 }}
                                    </span>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="mt-4 pt-3 border-t border-gray-100 flex items-center gap-2">
                                <button
                                    type="button"
                                    @click="openServiceModal(svc)"
                                    class="flex-1 rounded-xl bg-gradient-to-r from-brand-600 to-brand-700 hover:from-brand-700 hover:to-brand-800 text-white font-bold py-2.5 px-3 text-xs shadow-md shadow-brand-600/20 transition active:scale-[0.98] cursor-pointer text-center"
                                >
                                    Pilih & Lihat Hewan
                                </button>
                                <Link
                                    :href="route('catalog.index', svc.slug)"
                                    class="rounded-xl border border-gray-200 p-2.5 text-gray-600 hover:bg-gray-50 hover:text-brand-600 transition flex items-center justify-center"
                                    title="Buka Halaman Lengkap"
                                >
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="rounded-3xl border border-dashed border-gray-300 bg-white p-10 text-center shadow-xs">
                    <div class="h-12 w-12 mx-auto rounded-full bg-gray-100 flex items-center justify-center text-gray-400 mb-3">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h4 class="text-sm font-bold text-gray-900">Layanan Tidak Ditemukan</h4>
                    <p class="text-xs text-gray-500 mt-1">Coba kata kunci pencarian yang lain.</p>
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

        <div class="hidden md:block">
            <PublicFooter />
        </div>
    </div>
</template>
