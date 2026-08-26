<script setup>
import PublicFooter from '@/Components/PublicFooter.vue';
import PublicNavbar from '@/Components/PublicNavbar.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    service: { type: Object, required: true },
    product: { type: Object, required: true },
});

const rupiah = (value) =>
    new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(Number(value ?? 0));

const inStock = computed(() => props.product.stock > 0);
</script>

<template>
    <Head :title="`${product.name} — ${service.name}`" />

    <div class="min-h-screen bg-slate-50 font-sans pb-32 md:pb-0">
        <PublicNavbar />

        <!-- ================= MOBILE APP NATIVE TOP HEADER ================= -->
        <div class="block md:hidden bg-gradient-to-b from-slate-900 via-zinc-900 to-zinc-900 text-white pt-4 pb-5 px-4 rounded-b-[2rem] shadow-xl relative overflow-hidden mb-4">
            <div class="flex items-center justify-between relative z-10">
                <Link
                    :href="route('catalog.index', { service: service.slug })"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white/10 border border-white/15 text-xs font-bold text-zinc-200 hover:text-white"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span>Daftar Hewan</span>
                </Link>
                <span class="rounded-full bg-brand-500/90 px-3 py-1 text-[11px] font-bold uppercase tracking-wider text-white shadow-xs">
                    {{ service.name }}
                </span>
            </div>
        </div>

        <main class="py-2 md:py-10">
            <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
                <!-- Desktop Breadcrumb -->
                <nav class="hidden md:flex mb-6 items-center gap-2 text-xs font-semibold text-gray-500">
                    <Link href="/layanan" class="hover:text-brand-600">Semua Layanan</Link>
                    <span>/</span>
                    <Link
                        :href="route('catalog.index', { service: service.slug })"
                        class="hover:text-brand-600"
                    >
                        {{ service.name }}
                    </Link>
                    <span>/</span>
                    <span class="text-gray-900 font-bold">{{ product.name }}</span>
                </nav>

                <!-- Product Showcase Main Card -->
                <div class="overflow-hidden rounded-2xl md:rounded-3xl border border-gray-200/80 bg-white shadow-sm md:grid md:grid-cols-12 md:items-center">
                    
                    <!-- Product Image Showcase (5 Cols) -->
                    <div class="relative h-72 sm:h-96 md:h-full md:col-span-5 overflow-hidden bg-gray-900">
                        <img
                            v-if="product.primary_image_url"
                            :src="product.primary_image_url"
                            :alt="product.name"
                            class="h-full w-full object-cover transition duration-700 hover:scale-105"
                        />
                        <div
                            v-else
                            class="flex h-full w-full items-center justify-center bg-gray-100 text-sm text-gray-400"
                        >
                            Foto belum tersedia
                        </div>

                        <!-- Top Badges Overlay -->
                        <div class="absolute top-3.5 left-3.5 flex flex-wrap gap-1.5">
                            <span
                                v-if="product.weight_estimate_kg"
                                class="inline-flex items-center rounded-xl bg-slate-950/80 backdrop-blur-md px-3 py-1 text-xs font-extrabold text-white border border-white/10"
                            >
                                ~{{ Number(product.weight_estimate_kg) }} Kg
                            </span>
                        </div>

                        <div class="absolute top-3.5 right-3.5">
                            <span
                                class="inline-flex items-center rounded-xl px-3 py-1 text-xs font-black backdrop-blur-md shadow-sm"
                                :class="inStock ? 'bg-emerald-500/90 text-white' : 'bg-rose-500/90 text-white'"
                            >
                                {{ inStock ? `Tersedia (${product.stock})` : 'Stok Habis' }}
                            </span>
                        </div>
                    </div>

                    <!-- Product Info Details (7 Cols) -->
                    <div class="p-5 sm:p-8 md:col-span-7 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between gap-2">
                                <span class="rounded-lg bg-brand-50 px-2.5 py-1 text-[11px] font-bold text-brand-700">
                                    Paket {{ service.name }}
                                </span>
                                <span v-if="product.max_sohibul > 1" class="text-xs text-amber-600 font-semibold bg-amber-50 px-2.5 py-0.5 rounded-md">
                                    Maks. {{ product.max_sohibul }} Nama Sohibul
                                </span>
                            </div>

                            <h1 class="mt-2 text-2xl sm:text-3xl font-black tracking-tight text-gray-900">
                                {{ product.name }}
                            </h1>
                            
                            <div class="mt-3 flex items-baseline gap-2">
                                <span class="text-2xl sm:text-3xl font-black text-brand-600">
                                    {{ rupiah(product.price) }}
                                </span>
                                <span class="text-xs text-gray-400 font-medium">/ ekor</span>
                            </div>

                            <!-- Highlights Specifications -->
                            <div class="mt-5 grid grid-cols-2 gap-2.5">
                                <div class="rounded-2xl bg-gray-50 border border-gray-100 p-3">
                                    <span class="text-[10px] uppercase font-bold text-gray-400 block">Estimasi Bobot</span>
                                    <p class="text-sm font-extrabold text-gray-800 mt-0.5">
                                        {{ product.weight_estimate_kg ? `~${Number(product.weight_estimate_kg)} Kg` : 'Sesuai Standar Syari' }}
                                    </p>
                                </div>
                                <div class="rounded-2xl bg-gray-50 border border-gray-100 p-3">
                                    <span class="text-[10px] uppercase font-bold text-gray-400 block">Status Karantina</span>
                                    <p class="text-sm font-extrabold text-emerald-600 mt-0.5">
                                        Sehat & Lolos Uji
                                    </p>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="mt-5 border-t border-gray-100 pt-4">
                                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400">Deskripsi & Syariat</h3>
                                <p class="mt-2 text-xs sm:text-sm leading-relaxed text-gray-600 whitespace-pre-line">
                                    {{ product.description || 'Hewan ternak pilihan dengan perawatan terbaik, bebas dari cacat fisik, cukup umur, dan memenuhi kriteria syariat qurban & aqiqah.' }}
                                </p>
                            </div>
                        </div>

                        <!-- Action Button -->
                        <div class="mt-6 pt-5 border-t border-gray-100">
                            <Link
                                v-if="inStock"
                                :href="route('checkout.create', { service: service.slug, product: product.slug })"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-brand-600 to-brand-700 hover:from-brand-700 hover:to-brand-800 px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-brand-600/25 transition active:scale-[0.99] cursor-pointer"
                            >
                                <span>Pesan Hewan Ini Sekarang</span>
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </Link>
                            <button
                                v-else
                                type="button"
                                disabled
                                class="inline-flex w-full cursor-not-allowed items-center justify-center rounded-2xl bg-gray-200 px-6 py-3.5 text-sm font-bold text-gray-400"
                            >
                                Stok Hewan Sedang Habis
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Gallery Section -->
                <div v-if="product.gallery?.length" class="mt-8">
                    <h2 class="mb-3 text-sm font-bold uppercase tracking-wider text-gray-800">Foto Dokumentasi Hewan</h2>
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4">
                        <a
                            v-for="(imageUrl, i) in product.gallery"
                            :key="i"
                            :href="imageUrl"
                            target="_blank"
                            rel="noopener"
                            class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-xs hover:shadow-md transition"
                        >
                            <img
                                :src="imageUrl"
                                :alt="`${product.name} ${i + 1}`"
                                class="h-32 w-full object-cover"
                                loading="lazy"
                            />
                        </a>
                    </div>
                </div>
            </div>
        </main>

        <!-- Sembunyikan Footer di Mobile -->
        <div class="hidden md:block">
            <PublicFooter />
        </div>
    </div>
</template>

