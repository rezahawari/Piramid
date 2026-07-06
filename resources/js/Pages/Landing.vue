<script setup>
import HeroSlider from '@/Components/HeroSlider.vue';
import PublicFooter from '@/Components/PublicFooter.vue';
import PublicNavbar from '@/Components/PublicNavbar.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    services: Array,
    products: Array,
});

const rupiah = (v) =>
    new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(v);

// Atribut tampilan per layanan, meniru kartu "Layanan Kami" di referensi.
const serviceMeta = {
    qurban: {
        arabic: 'قربان',
        emoji: '🐄',
        features: [
            'Hewan sehat & layak qurban',
            'Hewan dipotong di RPH sesuai syariat',
            'Laporan video + foto',
            'Distribusi merata ke daerah yang membutuhkan',
        ],
    },
    aqiqah: {
        arabic: 'عقيقة',
        emoji: '🐐',
        features: [
            'Hewan dipotong di RPH sesuai syariat',
            'Bisa diantar ke rumah',
            'Bisa didistribusikan ke yatim/dhuafa',
            'Sertifikat + dokumentasi',
        ],
    },
    sedekah: {
        arabic: 'صدقة',
        emoji: '🐑',
        features: [
            'Tepat sasaran ke yang membutuhkan',
            'Laporan penyaluran transparan',
            'Bisa atas nama pribadi/keluarga',
            'Mudah & fleksibel dilakukan kapan saja',
        ],
    },
};
const metaFor = (slug) => serviceMeta[slug] ?? { arabic: '', emoji: '🐄', features: [] };

const productEmoji = (name) => {
    const n = name.toLowerCase();
    if (n.includes('sapi')) return '🐄';
    if (n.includes('domba')) return '🐑';
    return '🐐';
};

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
        <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="grid items-center gap-10 lg:grid-cols-2">
                <div>
                    <h2 class="text-3xl font-extrabold sm:text-4xl" style="text-wrap: balance">
                        Pilih Hewan Qurban &amp; Aqiqah Sesuai Kebutuhan
                    </h2>
                    <p class="mt-4 max-w-lg text-zinc-600">
                        Semua hewan kami diseleksi secara ketat: sehat, sesuai syariat, dan terawat oleh
                        peternak profesional. Harga transparan, pilihan beragam.
                    </p>
                    <div class="mt-8 hidden justify-center text-9xl lg:flex" aria-hidden="true">🐄</div>
                </div>
                <div class="flex flex-col gap-4">
                    <Link
                        v-for="p in products"
                        :key="p.id"
                        :href="`/layanan/qurban/produk/${p.slug}`"
                        class="group flex items-stretch overflow-hidden rounded-xl bg-night-soft text-white transition hover:-translate-y-0.5 hover:shadow-lg"
                    >
                        <span class="flex w-28 items-center justify-center bg-brand-100 text-5xl" aria-hidden="true">
                            {{ productEmoji(p.name) }}
                        </span>
                        <span class="flex flex-1 flex-col justify-center gap-1 px-5 py-4">
                            <span class="font-bold">{{ p.name }}</span>
                            <span v-if="p.weight_estimate_kg" class="text-xs text-zinc-400">
                                ±{{ Math.round(p.weight_estimate_kg) }} kg
                            </span>
                            <span class="mt-1 text-xs text-zinc-400">Mulai dari</span>
                            <span class="text-lg font-extrabold group-hover:text-brand-300">{{ rupiah(p.price) }}</span>
                        </span>
                    </Link>
                </div>
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
                            <span v-else aria-hidden="true">{{ metaFor(svc.slug).emoji }}</span>
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
