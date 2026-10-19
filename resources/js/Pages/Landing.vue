<script setup>
import AnimalPicker from '@/Components/AnimalPicker.vue';
import HeroSlider from '@/Components/HeroSlider.vue';
import PublicFooter from '@/Components/PublicFooter.vue';
import PublicNavbar from '@/Components/PublicNavbar.vue';
import { t } from '@/i18n';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';

const props = defineProps({
    services: Array,
    products: Array,
    legalities: {
        type: Array,
        default: () => [],
    },
    documentationGalleries: {
        type: Array,
        default: () => [],
    },
});

onMounted(() => {
    // Deteksi jika dibuka dari PWA Android standalone / APK TWA
    const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
    if (isStandalone) {
        router.visit('/login');
    }
});

const selectedCategory = ref('all');
const activeMediaModal = ref(null);
const activeLegalityModal = ref(null);

const filteredGalleries = computed(() => {
    if (!props.documentationGalleries || props.documentationGalleries.length === 0) {
        return [];
    }
    if (selectedCategory.value === 'all') {
        return props.documentationGalleries;
    }
    return props.documentationGalleries.filter((item) => item.category === selectedCategory.value);
});

// Atribut tampilan per layanan, meniru kartu "Layanan Kami" di referensi.
const serviceMeta = {
    qurban: {
        arabic: 'قربان',
        icon: '/images/service-icons/sapi.jpg',
        features: [
            'Hewan sehat & layak qurban',
            'Hewan dipotong di RPH sesuai syariat',
            'Laporan video + foto',
            'Distribusi merata ke daerah yang membutuhkan',
        ],
    },
    aqiqah: {
        arabic: 'عقيقة',
        icon: '/images/service-icons/kambing.jpg',
        features: [
            'Hewan dipotong di RPH sesuai syariat',
            'Bisa diantar ke rumah',
            'Bisa didistribusikan ke yatim/dhuafa',
            'Sertifikat + dokumentasi',
        ],
    },
    sedekah: {
        arabic: 'صدقة',
        icon: '/images/service-icons/domba.jpg',
        features: [
            'Tepat sasaran ke yang membutuhkan',
            'Laporan penyaluran transparan',
            'Bisa atas nama pribadi/keluarga',
            'Mudah & fleksibel dilakukan kapan saja',
        ],
    },
};
const metaFor = (slug) => serviceMeta[slug] ?? { arabic: '', icon: '/images/service-icons/sapi.jpg', features: [] };

const stats = [
    { title: '50K+', body: 'Keluarga percaya dengan kami', accent: true },
    { title: 'Amanah', body: 'Dipercaya ribuan keluarga untuk ibadah qurban & aqiqah.' },
    { title: 'Transparan', body: 'Laporan video & foto dari proses penyembelihan hingga distribusi.' },
    { title: 'Laporan', body: 'Setiap transaksi dilengkapi sertifikat, dokumentasi, dan status real-time.' },
];

const reasons = [
    'Lebih dari 5 tahun pengalaman',
    'Proses sesuai syariat & legal',
    'Laporan dokumentasi 100% transparan',
    'Mendukung misi sebar manfaat',
];

const heroSliderImages = [
    '/images/hero-slider/g1.jpeg',
    '/images/hero-slider/8.jpg',
    '/images/hero-slider/9.jpg',
    '/images/hero-slider/10.jpg',
    '/images/hero-slider/11.jpg',
    '/images/hero-slider/12.jpg',
    '/images/hero-slider/13.jpg',
    '/images/hero-slider/14.jpg',
    '/images/hero-slider/15.jpg',
    '/images/hero-slider/16.jpg',
    '/images/hero-slider/17.jpg',
];

const coverageCountries = [
    { name: 'Indonesia', flag: '🇮🇩' },
    { name: 'Singapore', flag: '🇸🇬' },
    { name: 'Australia', flag: '🇦🇺' },
    { name: 'New Zealand', flag: '🇳🇿' },
    { name: 'Taiwan', flag: '🇹🇼' },
    { name: 'Lithuania', flag: '🇱🇹' },
    { name: 'Netherlands', flag: '🇳🇱' },
    { name: 'Denmark', flag: '🇩🇰' },
    { name: 'United Kingdom', flag: '🇬🇧' },
    { name: 'United States', flag: '🇺🇸' },
];
</script>

<template>
    <Head title="Beranda" />

    <div class="bg-white font-sans text-zinc-900">
        <PublicNavbar />

        <!-- Hero -->
        <section class="relative overflow-hidden">
            <div class="mx-auto max-w-4xl px-4 pb-0 pt-14 text-center sm:px-6">
                <h1 class="font-script text-6xl text-night-soft sm:text-7xl">Piramid</h1>
                <p class="mx-auto mt-5 max-w-xl text-zinc-600">
                    Qurban &amp; Aqiqah Lebih Mudah, Amanah, dan Terjangkau! Sudah lebih dari 50.000+
                    keluarga mempercayakan ibadahnya bersama kami.
                </p>
            </div>

            <!-- Ilustrasi hero: latar masjid (full-bleed) + hewan qurban (di-crop sebatas paha) -->
            <img
                src="/assets/svgs/hero_bg.svg"
                alt=""
                aria-hidden="true"
                class="absolute inset-x-0 bottom-0 w-full opacity-10"
            />
            <div class="relative mx-auto mt-8 max-w-6xl px-4 sm:px-6 lg:px-8">
                <div class="relative mx-auto aspect-[2/1] w-full max-w-2xl overflow-hidden">
                    <img
                        src="/assets/svgs/hero.svg"
                        alt="Ilustrasi qurban Piramid"
                        class="absolute inset-x-0 top-0 w-full"
                    />
                </div>
            </div>

            <!-- Stats bar (kartu, overlap tipis sambung ke bawah ilustrasi hero) -->
            <div class="relative -mt-8 px-4 pb-8 sm:-mt-10 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-6xl rounded-3xl bg-night p-5 sm:p-8">
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <div
                            v-for="s in stats"
                            :key="s.title"
                            class="rounded-xl p-5"
                            :class="s.accent ? 'bg-brand-500 text-white' : 'bg-white text-night-soft'"
                        >
                            <p class="text-2xl font-extrabold">{{ s.title }}</p>
                            <p class="mt-1 text-sm" :class="s.accent ? 'text-brand-50' : 'text-zinc-600'">
                                {{ s.body }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Pilih hewan -->
        <section class="bg-zinc-50 py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <AnimalPicker :products="products">
                    <template #header>
                        <div class="max-w-lg">
                            <h2 class="text-3xl font-extrabold sm:text-4xl" style="text-wrap: balance">
                                Pilih Hewan Qurban &amp; Aqiqah Sesuai Kebutuhan
                            </h2>
                            <p class="mt-4 text-zinc-600">
                                Semua hewan kami diseleksi secara ketat: sehat, sesuai syariat, dan terawat oleh
                                peternak profesional. Harga transparan, pilihan beragam.
                            </p>
                        </div>
                    </template>
                </AnimalPicker>
            </div>
        </section>

        <!-- Layanan Kami (band hijau + layer masjid) -->
        <section class="relative overflow-hidden bg-brand-500 py-16">
            <img
                src="/assets/svgs/mosque_light.svg"
                alt=""
                aria-hidden="true"
                class="pointer-events-none absolute inset-x-0 bottom-0 w-full opacity-20 hidden md:block"
            />
            <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <h2 class="text-center text-3xl font-extrabold text-white">Layanan Kami</h2>
                <p class="mt-2 text-center text-brand-50">
                    Layanan Qurban &amp; Aqiqah yang Amanah dan Praktis
                </p>

                <div class="mt-10 grid gap-8 md:grid-cols-3">
                    <div v-for="svc in services" :key="svc.id" class="flex flex-col">
                        <Link
                            :href="`/layanan/${svc.slug}`"
                            class="flex h-44 items-center justify-center rounded-xl bg-white text-7xl shadow-sm transition hover:shadow-md"
                            :aria-label="`Lihat layanan ${svc.name}`"
                        >
                            <img
                                v-if="svc.cover_image_url"
                                :src="svc.cover_image_url"
                                :alt="svc.name"
                                class="h-full w-full rounded-xl object-cover"
                            />
                            <img
                                v-else
                                :src="metaFor(svc.slug).icon"
                                :alt="svc.name"
                                class="h-full w-full rounded-xl object-cover"
                            />
                        </Link>
                        <h3 class="mt-4 flex items-baseline gap-2 text-2xl font-bold text-white">
                            {{ svc.name }}
                            <span class="text-xl">{{ metaFor(svc.slug).arabic }}</span>
                        </h3>
                        <!-- <ul class="mt-3 space-y-2 text-sm text-brand-50">
                            <li
                                v-for="f in metaFor(svc.slug).features"
                                :key="f"
                                class="flex items-start gap-2"
                            >
                                <svg class="mt-0.5 h-4 w-4 shrink-0 fill-white" viewBox="0 0 20 20" aria-hidden="true">
                                    <path d="M7.6 13.2 4.4 10l-1.4 1.4 4.6 4.6 9.4-9.4L15.6 5z" />
                                </svg>
                                {{ f }}
                            </li>
                        </ul> -->
                    </div>
                </div>
            </div>
        </section>

        <!-- Kenapa harus Piramid -->
        <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="grid items-center gap-10 lg:grid-cols-2">
                <HeroSlider :images="heroSliderImages" />
                <div>
                    <h2 class="text-3xl font-extrabold sm:text-4xl">Kenapa Harus Piramid?</h2>
                    <p class="mt-4 max-w-lg text-zinc-600">
                        Kami hadir sebagai solusi ibadah qurban &amp; aqiqah yang praktis, transparan, dan
                        penuh keberkahan. Mengusung misi sosial untuk membantu distribusi ke pelosok dan
                        kaum dhuafa.
                    </p>
                    <ul class="mt-6 space-y-3">
                        <li v-for="r in reasons" :key="r" class="flex items-center gap-3">
                            <span class="h-2 w-2 shrink-0 rounded-full bg-sun-400" aria-hidden="true"></span>
                            <span class="font-semibold">{{ r }}</span>
                        </li>
                    </ul>
                    <Link
                        href="/layanan/qurban"
                        class="mt-8 inline-block rounded-md bg-sun-400 px-6 py-3 font-bold text-cocoa transition hover:bg-sun-500"
                    >
                        Pesan Sekarang
                    </Link>
                </div>
            </div>
        </section>

        <!-- Section Legalitas Perusahaan & Syariat -->
        <section class="bg-white py-20 border-t border-zinc-100">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Header Section Legalitas -->
                <div class="mx-auto max-w-3xl text-center">
                    <div class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3.5 py-1 text-xs font-bold text-emerald-700 border border-emerald-200/80 mb-3 shadow-2xs">
                        <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <span>Terverifikasi, Resmi &amp; Terpercaya</span>
                    </div>
                    <h2 class="text-3xl font-black text-zinc-900 sm:text-4xl tracking-tight">
                        Legalitas &amp; Sertifikasi Syariat
                    </h2>
                    <p class="mt-3 text-sm text-zinc-600 leading-relaxed">
                        Piramid beroperasi dengan izin resmi, mematuhi standar higienis Rumah Potong Hewan (RPH),
                        serta tersertifikasi syariat Islam agar ibadah qurban dan aqiqah Anda sah, tenang, dan berkah.
                    </p>
                </div>

                <!-- Dokumen Legalitas Grid Cards (Rata Tengah / Center Aligned) -->
                <div v-if="legalities && legalities.length > 0" class="mt-12 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="doc in legalities"
                        :key="doc.id"
                        class="group relative flex flex-col justify-between overflow-hidden rounded-3xl border border-zinc-200/80 bg-white p-7 text-center shadow-xs transition duration-300 hover:-translate-y-1 hover:border-brand-500/50 hover:shadow-xl"
                    >
                        <div class="flex flex-col items-center">
                            <!-- Top Format & Size Badge -->
                            <div class="flex items-center justify-center gap-2 mb-4">
                                <span class="rounded-lg px-2.5 py-0.5 text-[10px] font-black uppercase tracking-wider"
                                      :class="doc.file_type === 'pdf' ? 'bg-rose-100 text-rose-800' : 'bg-blue-100 text-blue-800'">
                                    {{ doc.file_type ? doc.file_type.toUpperCase() : 'DOKUMEN' }}
                                </span>
                                <span v-if="doc.file_size" class="rounded-lg bg-zinc-100 px-2 py-0.5 text-[10px] text-zinc-500 font-medium">
                                    {{ doc.file_size }}
                                </span>
                            </div>

                            <!-- Icon Badge Center -->
                            <div class="flex h-16 w-16 items-center justify-center rounded-2xl transition duration-300 group-hover:scale-110 shadow-2xs"
                                 :class="doc.file_type === 'pdf' ? 'bg-rose-50 text-rose-600 border border-rose-100' : 'bg-emerald-50 text-emerald-600 border border-emerald-100'">
                                <!-- PDF Icon -->
                                <svg v-if="doc.file_type === 'pdf'" class="h-8 w-8" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zM6 4h7v5h5v11H6V4zm8 9a1 1 0 0 1 1 1v1h1a1 1 0 1 1 0 2h-1v1a1 1 0 1 1-2 0v-4a1 1 0 0 1 1-1z" />
                                </svg>
                                <!-- Image Icon -->
                                <svg v-else class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>

                            <!-- Issuer & Title Center -->
                            <div class="mt-5 max-w-xs">
                                <span v-if="doc.issuer" class="text-[10px] font-bold uppercase tracking-widest text-brand-600 block mb-1">
                                    {{ doc.issuer }}
                                </span>
                                <h3 class="text-base font-extrabold text-zinc-900 group-hover:text-brand-600 transition leading-snug">
                                    {{ doc.title }}
                                </h3>
                            </div>

                            <!-- Document Number Center -->
                            <div v-if="doc.document_number" class="mt-3.5 inline-flex items-center justify-center gap-1.5 rounded-xl bg-zinc-50 px-3.5 py-1.5 text-xs font-mono text-zinc-700 border border-zinc-100 max-w-full">
                                <svg class="h-3.5 w-3.5 text-zinc-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                                </svg>
                                <span class="truncate font-semibold">{{ doc.document_number }}</span>
                            </div>

                            <!-- Description Center -->
                            <p v-if="doc.description" class="mt-3 text-xs text-zinc-600 line-clamp-2 leading-relaxed max-w-sm">
                                {{ doc.description }}
                            </p>
                        </div>

                        <!-- Card Actions: Pratinjau Dokumen Center -->
                        <div class="mt-6 pt-4 border-t border-zinc-100 flex flex-col sm:flex-row items-center justify-center gap-3">
                            <button
                                type="button"
                                @click="activeLegalityModal = doc"
                                class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-brand-500 px-4 py-2 text-xs font-bold text-white shadow-2xs hover:bg-brand-600 transition cursor-pointer w-full sm:w-auto"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <span>Lihat Pratinjau</span>
                            </button>

                            <span class="inline-flex items-center justify-center gap-1 rounded-xl bg-zinc-100 px-3 py-2 text-[11px] font-semibold text-zinc-500">
                                <svg class="h-3.5 w-3.5 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                Hanya Baca
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Empty State Fallback -->
                <div v-else class="mt-10 rounded-3xl border border-dashed border-zinc-200 bg-zinc-50/60 p-8 text-center max-w-lg mx-auto">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h4 class="mt-3 text-sm font-bold text-zinc-900">Legalitas &amp; Sertifikat Terverifikasi</h4>
                    <p class="mt-1 text-xs text-zinc-500 leading-relaxed">
                        Dokumen legalitas badan usaha, sertifikasi halal RPH, dan izin kurban sedang dalam sinkronisasi sistem.
                    </p>
                </div>
            </div>
        </section>

        <!-- Modal Pratinjau Dokumen Legalitas (Hanya Lihat / Proteksi Download) -->
        <div
            v-if="activeLegalityModal"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm select-none"
            @click.self="activeLegalityModal = null"
            @contextmenu.prevent
        >
            <div class="relative w-full max-w-4xl max-h-[92vh] flex flex-col rounded-3xl bg-white shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-200">
                <!-- Header Modal -->
                <div class="flex items-center justify-between border-b border-zinc-100 px-6 py-4 bg-zinc-50/90">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="rounded-md bg-brand-500 px-2 py-0.5 text-[9px] font-black uppercase text-white tracking-wider">
                                {{ activeLegalityModal.file_type ? activeLegalityModal.file_type.toUpperCase() : 'LEGALITAS' }}
                            </span>
                            <span v-if="activeLegalityModal.issuer" class="text-xs font-bold text-zinc-500 uppercase">
                                {{ activeLegalityModal.issuer }}
                            </span>
                        </div>
                        <h3 class="mt-1 text-base font-extrabold text-zinc-900 leading-snug">
                            {{ activeLegalityModal.title }}
                        </h3>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="hidden sm:inline-flex items-center gap-1 text-[11px] font-semibold text-zinc-500 bg-zinc-100 px-2.5 py-1 rounded-lg">
                            <svg class="h-3.5 w-3.5 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            Pratinjau Resmi (Dilindungi)
                        </span>
                        <button
                            type="button"
                            @click="activeLegalityModal = null"
                            class="h-8 w-8 rounded-full bg-zinc-200 flex items-center justify-center text-zinc-500 hover:bg-zinc-300 hover:text-zinc-900 transition text-sm font-bold cursor-pointer"
                        >
                            ✕
                        </button>
                    </div>
                </div>

                <!-- Body Document Preview -->
                <div class="flex-1 overflow-auto bg-zinc-950 p-4 flex items-center justify-center min-h-[420px]">
                    <!-- PDF Viewer dengan toolbar tersembunyi -->
                    <iframe
                        v-if="activeLegalityModal.file_type === 'pdf' || (activeLegalityModal.file_path && activeLegalityModal.file_path.toLowerCase().endsWith('.pdf'))"
                        :src="activeLegalityModal.file_path + '#toolbar=0&navpanes=0&scrollbar=1'"
                        class="w-full h-[70vh] rounded-2xl border border-white/10"
                    ></iframe>

                    <!-- Image Viewer (PNG / JPG) -->
                    <img
                        v-else
                        :src="activeLegalityModal.file_path"
                        :alt="activeLegalityModal.title"
                        class="max-h-[75vh] max-w-full rounded-2xl object-contain shadow-2xl"
                    />
                </div>

                <!-- Footer Modal Details -->
                <div v-if="activeLegalityModal.document_number || activeLegalityModal.description" class="bg-zinc-50 px-6 py-3 border-t border-zinc-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-xs text-zinc-600">
                    <span v-if="activeLegalityModal.document_number" class="font-mono font-semibold">
                        No. Sertifikat: {{ activeLegalityModal.document_number }}
                    </span>
                    <span v-if="activeLegalityModal.issued_date" class="text-zinc-500">
                        Tanggal: {{ activeLegalityModal.issued_date }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Section Galeri Dokumentasi & Edukasi -->
        <section class="bg-zinc-50 py-20">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-3xl text-center">
                    <span class="text-xs font-bold uppercase tracking-widest text-brand-600">Dokumentasi &amp; Edukasi Syariat</span>
                    <h2 class="mt-2 text-3xl font-black text-zinc-900 sm:text-4xl">
                        Galeri Proses Ibadah &amp; Distribusi
                    </h2>
                    <p class="mt-3 text-sm text-zinc-600 leading-relaxed">
                        Saksikan bagaimana tim kami menjalankan amanah ibadah qurban dan aqiqah Anda secara profesional,
                        higienis, dan sesuai syariat Islam dari peternakan hingga disalurkan ke pelosok nusantara.
                    </p>

                    <!-- Kategori Filter Pills -->
                    <div class="mt-8 flex flex-wrap justify-center gap-2">
                        <button
                            type="button"
                            @click="selectedCategory = 'all'"
                            class="rounded-xl px-4 py-2 text-xs font-bold transition shadow-xs"
                            :class="selectedCategory === 'all' ? 'bg-brand-500 text-white' : 'bg-white text-zinc-600 hover:bg-zinc-100 border border-zinc-200'"
                        >
                            Semua Dokumentasi
                        </button>
                    </div>
                </div>

                <!-- Pure Photo Gallery Grid (Berjejer Bersih) -->
                <div v-if="filteredGalleries.length" class="mt-12 grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 sm:gap-4">
                    <div
                        v-for="item in filteredGalleries"
                        :key="item.id"
                        class="group relative aspect-square overflow-hidden rounded-2xl bg-zinc-900 shadow-sm cursor-pointer"
                        @click="activeMediaModal = item"
                    >
                        <!-- Media View (Image / Video / YouTube Thumbnail) -->
                        <img
                            v-if="item.type === 'image' && item.file_url"
                            :src="item.file_url"
                            :alt="item.title"
                            class="h-full w-full object-cover transition duration-500 group-hover:scale-110"
                        />

                        <video
                            v-else-if="item.type === 'video' && item.file_url"
                            :src="item.file_url"
                            class="h-full w-full object-cover pointer-events-none transition duration-500 group-hover:scale-110"
                        ></video>

                        <iframe
                            v-else-if="item.type === 'youtube' && item.youtube_url"
                            :src="item.youtube_url"
                            class="h-full w-full pointer-events-none"
                        ></iframe>

                        <!-- Play Icon Indicator for Videos -->
                        <div
                            v-if="item.type === 'video' || item.type === 'youtube'"
                            class="absolute inset-0 flex items-center justify-center bg-black/20 group-hover:bg-transparent transition"
                        >
                            <div class="flex h-10 w-10 sm:h-12 sm:w-12 items-center justify-center rounded-full bg-brand-500/90 text-white shadow-lg backdrop-blur-xs transition duration-300 group-hover:scale-110">
                                <svg class="h-5 w-5 sm:h-6 sm:w-6 ml-0.5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z" />
                                </svg>
                            </div>
                        </div>

                        <!-- Hover Overlay with Title & Category -->
                        <div class="absolute inset-0 flex flex-col justify-end bg-gradient-to-t from-black/85 via-black/30 to-transparent p-4 opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                            <span class="inline-block self-start rounded-md bg-brand-500/90 px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider text-white backdrop-blur-xs">
                                {{ item.category }}
                            </span>
                            <h4 class="mt-1.5 text-xs sm:text-sm font-bold text-white line-clamp-2 leading-snug">
                                {{ item.title }}
                            </h4>
                        </div>
                    </div>
                </div>

                <!-- Fallback YouTube Default jika galeri kosong -->
                <div v-else class="mx-auto mt-10 max-w-4xl overflow-hidden rounded-2xl bg-zinc-900 shadow-2xl ring-1 ring-zinc-900/10">
                    <div class="relative aspect-video w-full">
                        <iframe
                            class="absolute inset-0 h-full w-full rounded-2xl border-0"
                            src="https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ?rel=0"
                            title="Video Dokumentasi Piramid Qurban"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            allowfullscreen
                        ></iframe>
                    </div>
                </div>
            </div>
        </section>

        <!-- Media Popup Modal -->
        <div
            v-if="activeMediaModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4 backdrop-blur-md"
            @click.self="activeMediaModal = null"
        >
            <div class="relative w-full max-w-4xl overflow-hidden rounded-2xl bg-zinc-900 shadow-2xl">
                <!-- Close Button -->
                <button
                    type="button"
                    class="absolute right-4 top-4 z-10 flex h-9 w-9 items-center justify-center rounded-full bg-black/60 text-white hover:bg-black"
                    @click="activeMediaModal = null"
                >
                    &times;
                </button>

                <!-- Media Content -->
                <div class="relative aspect-video w-full bg-black flex items-center justify-center">
                    <img
                        v-if="activeMediaModal.type === 'image'"
                        :src="activeMediaModal.file_url"
                        :alt="activeMediaModal.title"
                        class="h-full w-full object-contain"
                    />
                    <video
                        v-else-if="activeMediaModal.type === 'video'"
                        :src="activeMediaModal.file_url"
                        class="h-full w-full"
                        controls
                        autoplay
                    ></video>
                    <iframe
                        v-else-if="activeMediaModal.type === 'youtube'"
                        :src="activeMediaModal.youtube_url"
                        class="h-full w-full"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen
                    ></iframe>
                </div>

                <!-- Media Details -->
                <div class="bg-white p-5">
                    <div class="flex items-center gap-2">
                        <span class="rounded-lg bg-brand-50 border border-brand-100 px-2.5 py-0.5 text-[10px] font-bold uppercase text-brand-700">
                            {{ activeMediaModal.category }}
                        </span>
                        <h3 class="text-base font-bold text-zinc-900">{{ activeMediaModal.title }}</h3>
                    </div>
                    <p v-if="activeMediaModal.description" class="mt-2 text-xs text-zinc-600 leading-relaxed">
                        {{ activeMediaModal.description }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Cakupan Wilayah -->
        <section class="overflow-hidden pt-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid gap-8 lg:grid-cols-2 lg:items-start">
                    <div>
                        <h2 class="text-3xl font-extrabold sm:text-4xl">Cakupan Wilayah</h2>
                        <p class="mt-4 max-w-lg text-zinc-600">
                            Temukan area yang terjangkau oleh layanan kami. Kami terus memperluas jangkauan
                            demi pelayanan yang lebih baik.
                        </p>
                    </div>
                    <div class="grid grid-cols-2 gap-x-6 gap-y-3 sm:grid-cols-4 lg:grid-cols-2">
                        <div
                            v-for="c in coverageCountries"
                            :key="c.name"
                            class="flex items-center gap-2 text-sm font-semibold text-zinc-700"
                        >
                            <span class="text-xl" aria-hidden="true">{{ c.flag }}</span>
                            {{ c.name }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="relative mt-10">
                <img
                    src="/images/coverage/map-full.png"
                    alt="Peta cakupan wilayah Piramid"
                    class="w-full"
                />
            </div>
        </section>

        <!-- Penutup -->
        <section class="bg-night py-16">
            <div class="mx-auto max-w-3xl px-4 text-center sm:px-6">
                <h2 class="text-2xl font-extrabold text-white sm:text-3xl">
                    Yuk, Tunaikan Qurban &amp; Aqiqah Bersama Piramid
                </h2>
                <p class="mt-3 text-brand-50">
                    Amanah, transparan, dan menjangkau hingga pelosok negeri &mdash; kapan pun, di mana pun.
                </p>
                <Link
                    href="/layanan/qurban"
                    class="mt-6 inline-block rounded-md bg-sun-400 px-6 py-3 font-bold text-cocoa transition hover:bg-sun-500"
                >
                    Pesan Sekarang
                </Link>
            </div>
        </section>

        <PublicFooter />
    </div>
</template>
