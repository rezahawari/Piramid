<script setup>
import PublicFooter from '@/Components/PublicFooter.vue';
import PublicNavbar from '@/Components/PublicNavbar.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    service: { type: Object, required: true },
    products: { type: Array, default: () => [] },
    otherServices: { type: Array, default: () => [] },
});

const searchQuery = ref('');
const sortBy = ref('name_asc'); // 'name_asc' | 'price_asc' | 'price_desc' | 'weight_desc'

const rupiah = (value) =>
    new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(Number(value ?? 0));

const serviceIcons = {
    qurban: '/images/service-icons/sapi.jpg',
    aqiqah: '/images/service-icons/kambing.jpg',
    sedekah: '/images/service-icons/domba.jpg',
};

const defaultServiceMeta = {
    qurban: {
        arabic: 'قربan',
        tagline: 'Tunaikan Ibadah Qurban Penuh Berkah',
        highlights: [
            'Pemotongan sesuai syariat di RPH bersertifikasi',
            'Hewan sehat, bebas cacat, dan cukup umur',
            'Laporan dokumentasi foto & video penyembelihan',
            'Penyaluran tepat sasaran hingga pelosok negeri',
        ],
    },
    aqiqah: {
        arabic: 'عقيقة',
        tagline: 'Sambut Buah Hati dengan Aqiqah Praktis & Syari',
        highlights: [
            'Hewan jantan/betina terbaik pilihan',
            'Dapat diantar matang/mentah ke alamat Anda',
            'Bisa disalurkan langsung ke panti asuhan/dhuafa',
            'Dilengkapi sertifikat & dokumentasi lengkap',
        ],
    },
    sedekah: {
        arabic: 'صدقة',
        tagline: 'Berbagi Kebahagiaan Melalui Sedekah Ternak',
        highlights: [
            'Bisa atas nama pribadi, keluarga, atau orang tua',
            'Pemberdayaan peternak lokal',
            'Laporan transparansi penyaluran berkala',
            'Fleksibel ditunaikan kapan saja',
        ],
    },
};

const meta = computed(() => {
    return (
        defaultServiceMeta[props.service.slug] ?? {
            arabic: '',
            tagline: 'Layanan Ibadah Hewan Terbaik dan Terpercaya',
            highlights: [
                'Hewan terawat dan memenuhi standar mutu',
                'Proses transparan dan terverifikasi',
                'Dokumentasi lengkap setiap tahapan',
            ],
        }
    );
});

const filteredProducts = computed(() => {
    let result = [...props.products];

    if (searchQuery.value.trim()) {
        const q = searchQuery.value.toLowerCase();
        result = result.filter(
            (p) =>
                p.name.toLowerCase().includes(q) ||
                (p.description && p.description.toLowerCase().includes(q))
        );
    }

    if (sortBy.value === 'price_asc') {
        result.sort((a, b) => Number(a.price) - Number(b.price));
    } else if (sortBy.value === 'price_desc') {
        result.sort((a, b) => Number(b.price) - Number(a.price));
    } else if (sortBy.value === 'weight_desc') {
        result.sort(
            (a, b) =>
                Number(b.weight_estimate_kg || 0) -
                Number(a.weight_estimate_kg || 0)
        );
    } else {
        result.sort((a, b) => a.name.localeCompare(b.name));
    }

    return result;
});
</script>

<template>
    <Head :title="`Layanan ${service.name} - Piramid`" />

    <div class="min-h-screen bg-brand-50/40 font-sans text-gray-900 pb-28 md:pb-0">
        <PublicNavbar class="hidden md:flex" />

        <!-- ================= MOBILE APP NATIVE HEADER (Tampil Khusus di Layar HP) ================= -->
        <div class="block md:hidden bg-gradient-to-b from-slate-900 via-zinc-900 to-zinc-900 text-white pt-4 pb-6 px-4 rounded-b-[2rem] shadow-xl relative overflow-hidden">
            <div class="flex items-center justify-between relative z-10 mb-3">
                <Link
                    href="/layanan"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white/10 border border-white/15 text-xs font-bold text-zinc-200 hover:text-white"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span>Layanan</span>
                </Link>
                <span class="rounded-full bg-brand-500/90 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-white">
                    {{ service.name }}
                </span>
            </div>

            <div class="relative z-10">
                <h1 class="text-2xl font-black text-white tracking-tight leading-tight">{{ service.name }}</h1>
                <p class="text-xs text-amber-300 font-medium mt-0.5">{{ meta.tagline }}</p>
                <p class="text-[11px] text-zinc-300 line-clamp-2 mt-2 leading-relaxed">
                    {{ service.description || 'Pilih hewan terbaik yang telah terawat dan memenuhi kriteria syariat.' }}
                </p>
            </div>
        </div>

        <!-- ================= DESKTOP HERO SECTION (Tampil Khusus di Layar Besar) ================= -->
        <section class="hidden md:block relative overflow-hidden bg-zinc-900 text-white">
            <!-- Background Image with Overlay -->
            <div class="absolute inset-0 z-0">
                <img
                    v-if="service.cover_image_url"
                    :src="service.cover_image_url"
                    :alt="service.name"
                    class="h-full w-full object-cover opacity-25"
                />
                <img
                    v-else-if="serviceIcons[service.slug]"
                    :src="serviceIcons[service.slug]"
                    :alt="service.name"
                    class="h-full w-full object-cover opacity-25"
                />
                <div class="absolute inset-0 bg-gradient-to-t from-zinc-950 via-zinc-900/80 to-transparent"></div>
            </div>

            <div class="relative z-10 mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
                <div class="grid grid-cols-1 gap-12 lg:grid-cols-12 lg:items-center">
                    <!-- Left Column: Title & Description -->
                    <div class="lg:col-span-7">
                        <div class="flex items-center gap-3">
                            <Link
                                href="/layanan"
                                class="inline-flex items-center text-xs font-semibold uppercase tracking-wider text-brand-300 hover:text-brand-200 transition"
                            >
                                &larr; Semua Layanan
                            </Link>
                            <span class="rounded-full bg-brand-500/90 px-3 py-1 text-xs font-bold uppercase tracking-wider text-white">
                                Layanan Ibadah
                            </span>
                        </div>

                        <h1 class="mt-4 text-4xl font-extrabold tracking-tight sm:text-5xl lg:text-6xl text-white">
                            {{ service.name }}
                        </h1>
                        <p class="mt-2 text-lg font-medium text-brand-300">
                            {{ meta.tagline }}
                        </p>

                        <p class="mt-4 text-base leading-relaxed text-zinc-300 max-w-2xl">
                            {{
                                service.description ||
                                'Tunaikan ibadah qurban, aqiqah, dan sedekah dengan kemudahan transaksi, pelacakan proses syari real-time, dan dokumentasi langsung ke tangan Anda.'
                            }}
                        </p>

                        <!-- Service Highlights / Keunggulan -->
                        <div class="mt-8 grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <div
                                v-for="(hl, idx) in meta.highlights"
                                :key="idx"
                                class="flex items-start gap-2.5 rounded-lg bg-white/5 p-3 backdrop-blur-sm border border-white/10"
                            >
                                <svg class="mt-0.5 h-4 w-4 shrink-0 text-brand-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-xs font-medium text-zinc-200">{{ hl }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Card Cover Image Showcase -->
                    <div class="lg:col-span-5">
                        <div class="relative mx-auto max-w-md overflow-hidden rounded-2xl border border-white/20 bg-zinc-800/80 p-2 shadow-2xl backdrop-blur">
                            <div class="aspect-[4/3] w-full overflow-hidden rounded-xl bg-zinc-900">
                                <img
                                    v-if="service.cover_image_url"
                                    :src="service.cover_image_url"
                                    :alt="service.name"
                                    class="h-full w-full object-cover"
                                />
                                <img
                                    v-else-if="serviceIcons[service.slug]"
                                    :src="serviceIcons[service.slug]"
                                    :alt="service.name"
                                    class="h-full w-full object-cover"
                                />
                                <div v-else class="flex h-full w-full items-center justify-center text-zinc-500">
                                    Gambar Sampul
                                </div>
                            </div>
                            <div class="p-4 flex items-center justify-between">
                                <div>
                                    <span class="text-xs uppercase tracking-wider text-zinc-400">Total Hewan Tersedia</span>
                                    <p class="text-xl font-bold text-white">{{ products.length }} Pilihan Hewan</p>
                                </div>
                                <a
                                    href="#daftar-hewan"
                                    class="rounded-xl bg-brand-500 px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-brand-600"
                                >
                                    Pilih Hewan &darr;
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- MAIN SECTION: Filter & Daftar Produk Hewan -->
        <main id="daftar-hewan" class="py-14">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Section Title & Filter Bar -->
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between border-b border-gray-200/80 pb-6">
                    <div>
                        <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-gray-900">
                            Daftar Hewan {{ service.name }}
                        </h2>
                        <p class="mt-1 text-sm text-gray-500">
                            Pilih hewan yang sesuai dengan kriteria dan kebutuhan ibadah Anda.
                        </p>
                    </div>

                    <!-- Search & Sort Controls -->
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                        <div class="relative">
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Cari nama hewan..."
                                class="w-full sm:w-64 rounded-xl border-gray-300 bg-white py-2 pl-9 pr-4 text-xs shadow-sm focus:border-brand-500 focus:ring-brand-500"
                            />
                            <svg class="absolute left-3 top-2.5 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>

                        <select
                            v-model="sortBy"
                            class="rounded-xl border-gray-300 bg-white py-2 pl-3 pr-8 text-xs shadow-sm focus:border-brand-500 focus:ring-brand-500"
                        >
                            <option value="name_asc">Nama (A - Z)</option>
                            <option value="price_asc">Harga Terendah</option>
                            <option value="price_desc">Harga Tertinggi</option>
                            <option value="weight_desc">Bobot Terberat</option>
                        </select>
                    </div>
                </div>

                <!-- Products Grid -->
                <div
                    v-if="filteredProducts.length"
                    class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3"
                >
                    <div
                        v-for="product in filteredProducts"
                        :key="product.id"
                        class="group flex flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition duration-200 hover:-translate-y-1 hover:border-brand-500/40 hover:shadow-xl"
                    >
                        <!-- Product Image Box -->
                        <div class="relative h-56 w-full overflow-hidden bg-gray-100">
                            <img
                                v-if="product.primary_image_url"
                                :src="product.primary_image_url"
                                :alt="product.name"
                                class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                                loading="lazy"
                            />
                            <div
                                v-else
                                class="flex h-full w-full items-center justify-center bg-gray-100 text-sm text-gray-400"
                            >
                                Foto belum tersedia
                            </div>

                            <!-- Weight / Badge Badge -->
                            <div class="absolute left-3 top-3 flex flex-wrap gap-1.5">
                                <span
                                    v-if="product.weight_estimate_kg"
                                    class="inline-flex items-center rounded-lg bg-zinc-900/80 px-2.5 py-1 text-xs font-semibold text-white backdrop-blur"
                                >
                                    ~{{ product.weight_estimate_kg }} Kg
                                </span>
                            </div>

                            <div class="absolute right-3 top-3">
                                <span
                                    class="inline-flex items-center rounded-lg px-2.5 py-1 text-xs font-bold shadow-sm backdrop-blur"
                                    :class="product.stock > 0 ? 'bg-emerald-500/90 text-white' : 'bg-rose-500/90 text-white'"
                                >
                                    {{ product.stock > 0 ? `Tersedia (${product.stock})` : 'Stok Habis' }}
                                </span>
                            </div>
                        </div>

                        <!-- Product Content Details -->
                        <div class="flex flex-1 flex-col p-6">
                            <div class="flex-1">
                                <h3 class="text-xl font-bold text-gray-900 group-hover:text-brand-600 transition">
                                    {{ product.name }}
                                </h3>
                                <p class="mt-2 line-clamp-2 text-xs leading-relaxed text-gray-500">
                                    {{ product.description || 'Hewan sehat berkualitas, telah memenuhi kriteria syariat yang ketat.' }}
                                </p>
                            </div>

                            <!-- Price Tag -->
                            <div class="mt-6 border-t border-gray-100 pt-4 flex items-baseline justify-between">
                                <div>
                                    <span class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Harga Ibadah</span>
                                    <p class="text-2xl font-black tracking-tight text-brand-600">
                                        {{ rupiah(product.price) }}
                                    </p>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="mt-4 grid grid-cols-2 gap-2">
                                <Link
                                    :href="route('catalog.show', { service: service.slug, product: product.slug })"
                                    class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-xs font-bold text-gray-700 transition hover:bg-gray-50"
                                >
                                    Detail Hewan
                                </Link>
                                <Link
                                    v-if="product.stock > 0"
                                    :href="route('checkout.create', { service: service.slug, product: product.slug })"
                                    class="inline-flex items-center justify-center rounded-xl bg-brand-500 px-3 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-brand-600"
                                >
                                    Pesan Sekarang
                                </Link>
                                <button
                                    v-else
                                    disabled
                                    class="inline-flex cursor-not-allowed items-center justify-center rounded-xl bg-gray-200 px-3 py-2.5 text-xs font-bold text-gray-400"
                                >
                                    Habis
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div
                    v-else
                    class="mt-8 rounded-2xl border border-dashed border-gray-300 bg-white p-12 text-center shadow-sm"
                >
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <h3 class="mt-3 text-base font-semibold text-gray-900">Tidak ada hewan ditemukan</h3>
                    <p class="mt-1 text-xs text-gray-500">
                        {{ searchQuery ? 'Coba ubah kata kunci pencarian Anda.' : 'Belum ada produk hewan yang ditautkan ke layanan ini.' }}
                    </p>
                </div>

                <!-- Other Services Navigation -->
                <div v-if="otherServices.length" class="mt-20 border-t border-gray-200/80 pt-12">
                    <h3 class="text-xl font-bold text-gray-900">Jelajahi Layanan Lainnya</h3>
                    <div class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        <Link
                            v-for="other in otherServices"
                            :key="other.id"
                            :href="route('catalog.index', other.slug)"
                            class="group flex items-center gap-4 rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition hover:border-brand-500/50 hover:shadow-md"
                        >
                            <img
                                v-if="other.cover_image_url"
                                :src="other.cover_image_url"
                                :alt="other.name"
                                class="h-16 w-16 shrink-0 rounded-lg object-cover"
                            />
                            <img
                                v-else-if="serviceIcons[other.slug]"
                                :src="serviceIcons[other.slug]"
                                :alt="other.name"
                                class="h-16 w-16 shrink-0 rounded-lg object-cover"
                            />
                            <div v-else class="flex h-16 w-16 shrink-0 items-center justify-center rounded-lg bg-gray-100 text-xs text-gray-400">
                                Layanan
                            </div>
                            <div>
                                <h4 class="text-base font-bold text-gray-900 group-hover:text-brand-600 transition">
                                    {{ other.name }}
                                </h4>
                                <span class="mt-1 inline-flex items-center text-xs font-semibold text-brand-600">
                                    Lihat Hewan &rarr;
                                </span>
                            </div>
                        </Link>
                    </div>
                </div>
            </div>
        </main>

        <PublicFooter />
    </div>
</template>
