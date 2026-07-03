<script setup>
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

            <!-- Ilustrasi hero sederhana + siluet masjid -->
            <div class="relative mx-auto mt-8 flex h-48 max-w-5xl items-end justify-center gap-6 sm:h-56">
                <svg class="absolute inset-x-0 bottom-0 h-full w-full text-brand-100" viewBox="0 0 1000 200" preserveAspectRatio="none" aria-hidden="true">
                    <path fill="currentColor" d="M0 200V120h60l20-40 20 40h80V90l50-60 50 60v30h90l30-70 30 70h110V80l40-50 40 50v40h100l25-55 25 55h70V110l45-55 45 55v90z" opacity=".5" />
                </svg>
                <span class="relative text-7xl sm:text-8xl" aria-hidden="true">🐐</span>
                <span class="relative text-8xl sm:text-9xl" aria-hidden="true">🧕</span>
                <span class="relative text-8xl sm:text-9xl" aria-hidden="true">🐄</span>
            </div>

            <!-- Stats bar -->
            <div class="bg-night py-8">
                <div class="mx-auto grid max-w-6xl gap-4 px-4 sm:grid-cols-2 sm:px-6 lg:grid-cols-4 lg:px-8">
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

        <!-- Layanan Kami (band hijau) -->
        <section class="bg-brand-500 py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
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
                <div class="hidden justify-center text-9xl lg:flex" aria-hidden="true">🕌</div>
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

        <PublicFooter />
    </div>
</template>
