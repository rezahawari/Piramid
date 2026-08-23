<script setup>
import AnimalPicker from '@/Components/AnimalPicker.vue';
import HeroSlider from '@/Components/HeroSlider.vue';
import PublicFooter from '@/Components/PublicFooter.vue';
import PublicNavbar from '@/Components/PublicNavbar.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    services: Array,
    products: Array,
    documentationGalleries: {
        type: Array,
        default: () => [],
    },
});

const selectedCategory = ref('all');
const activeMediaModal = ref(null);

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
                class="pointer-events-none absolute inset-x-0 bottom-0 w-full opacity-20"
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
                        <ul class="mt-3 space-y-2 text-sm text-brand-50">
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
                        </ul>
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
                        <button
                            type="button"
                            @click="selectedCategory = 'qurban'"
                            class="rounded-xl px-4 py-2 text-xs font-bold transition shadow-xs"
                            :class="selectedCategory === 'qurban' ? 'bg-brand-500 text-white' : 'bg-white text-zinc-600 hover:bg-zinc-100 border border-zinc-200'"
                        >
                            Qurban
                        </button>
                        <button
                            type="button"
                            @click="selectedCategory = 'aqiqah'"
                            class="rounded-xl px-4 py-2 text-xs font-bold transition shadow-xs"
                            :class="selectedCategory === 'aqiqah' ? 'bg-brand-500 text-white' : 'bg-white text-zinc-600 hover:bg-zinc-100 border border-zinc-200'"
                        >
                            Aqiqah
                        </button>
                        <button
                            type="button"
                            @click="selectedCategory = 'edukasi'"
                            class="rounded-xl px-4 py-2 text-xs font-bold transition shadow-xs"
                            :class="selectedCategory === 'edukasi' ? 'bg-brand-500 text-white' : 'bg-white text-zinc-600 hover:bg-zinc-100 border border-zinc-200'"
                        >
                            Edukasi Syariat
                        </button>
                        <button
                            type="button"
                            @click="selectedCategory = 'distribusi'"
                            class="rounded-xl px-4 py-2 text-xs font-bold transition shadow-xs"
                            :class="selectedCategory === 'distribusi' ? 'bg-brand-500 text-white' : 'bg-white text-zinc-600 hover:bg-zinc-100 border border-zinc-200'"
                        >
                            Penyaluran Pelosok
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
