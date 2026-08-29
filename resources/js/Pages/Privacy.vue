<script setup>
import BrandLogo from '@/Components/BrandLogo.vue';
import PublicNavbar from '@/Components/PublicNavbar.vue';
import { Head, Link } from '@inertiajs/vue3';

const lastUpdated = '29 Agustus 2026';
</script>

<template>
    <Head title="Kebijakan Privasi (Privacy Policy) - Piramid" />

    <div class="min-h-screen bg-slate-50 text-slate-900 font-sans pb-28 md:pb-16 selection:bg-brand-500 selection:text-white">
        <PublicNavbar />

        <!-- Header Hero Section -->
        <header class="bg-gradient-to-b from-slate-950 via-zinc-900 to-zinc-900 text-white pt-8 pb-12 px-4 sm:px-6 lg:px-8 rounded-b-[2.5rem] shadow-xl relative overflow-hidden">
            <!-- Ambient Glow -->
            <div class="absolute -top-24 -right-24 w-72 h-72 bg-amber-500/15 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -left-24 w-72 h-72 bg-brand-500/15 rounded-full blur-3xl pointer-events-none"></div>

            <div class="mx-auto max-w-4xl relative z-10 text-center">
                <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur px-3 py-1.5 rounded-full text-xs font-bold text-amber-300 border border-white/10 mb-4 shadow-inner">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <span>Transparansi & Keamanan Data</span>
                </div>

                <h1 class="text-2xl sm:text-4xl font-black tracking-tight text-white leading-tight">
                    Kebijakan Privasi (Privacy Policy)
                </h1>
                <p class="mt-2 text-xs sm:text-sm text-zinc-300 max-w-xl mx-auto">
                    Komitmen resmi <strong>PT Delapan Penjuru Piramida (Piramid)</strong> dalam melindungi dan menjaga privasi data Anda saat menggunakan layanan kami.
                </p>
                <p class="mt-4 text-[11px] text-zinc-400 font-mono">
                    Terakhir diperbarui: {{ lastUpdated }}
                </p>
            </div>
        </header>

        <!-- Main Content Container -->
        <main class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 -mt-6 relative z-20">
            <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-lg border border-slate-200/80 space-y-8 text-xs sm:text-sm leading-relaxed text-slate-700">

                <!-- Ringkasan Singkat -->
                <div class="rounded-2xl bg-amber-50/80 border border-amber-200/70 p-4 sm:p-5 text-amber-900">
                    <h2 class="text-sm sm:text-base font-bold text-amber-950 flex items-center gap-2">
                        <span>💡</span> Ringkasan Komitmen Kami
                    </h2>
                    <p class="mt-1.5 text-xs sm:text-[13px] leading-relaxed">
                        Kami menghargai privasi Anda. Data yang kami kumpulkan semata-mata digunakan untuk memproses pesanan ibadah (Qurban & Aqiqah), verifikasi pembayaran, pelafalan niat shohibul ibadah, pengiriman daging, serta pengiriman bukti dokumentasi video dan foto. <strong>Kami tidak pernah menjual data pribadi Anda kepada pihak ketiga mana pun.</strong>
                    </p>
                </div>

                <!-- 1. Informasi yang Kami Kumpulkan -->
                <section class="space-y-3">
                    <h2 class="text-base sm:text-lg font-bold text-slate-900 flex items-center gap-2 border-b border-slate-100 pb-2">
                        <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-brand-50 text-brand-600 text-xs font-black">1</span>
                        Informasi yang Kami Kumpulkan
                    </h2>
                    <p>Kami mengumpulkan beberapa jenis data saat Anda mendaftar, mengakses, atau bertransaksi di aplikasi <strong>Piramid</strong>:</p>
                    <ul class="list-disc pl-5 space-y-2 text-slate-600">
                        <li><strong>Data Akun:</strong> Nama lengkap, alamat email, nomor telepon/WhatsApp, dan kata sandi yang dienkripsi.</li>
                        <li><strong>Data Ibadah (Shohibul Qurban/Aqiqah):</strong> Nama-nama shohibul ibadah yang Anda daftarkan untuk dibacakan doa dan niat saat prosesi penyembelihan hewan.</li>
                        <li><strong>Data Pengiriman:</strong> Alamat lengkap, catatan khusus, dan nomor kontak penerima manfaat atau pemesan jika memilih opsi "Kirim ke Alamat Sendiri".</li>
                        <li><strong>Data Pembayaran:</strong> Bukti transfer bank (jika transfer manual), atau status transaksi dari payment gateway resmi (Midtrans). Kami <em>tidak</em> menyimpan data kartu kredit atau PIN perbankan Anda.</li>
                        <li><strong>Izin Notifikasi (Push Notifications):</strong> Token perangkat (endpoint Web Push/FCM) untuk mengirimkan notifikasi pembaruan status pesanan dan ketersediaan video dokumentasi.</li>
                    </ul>
                </section>

                <!-- 2. Penggunaan Informasi -->
                <section class="space-y-3">
                    <h2 class="text-base sm:text-lg font-bold text-slate-900 flex items-center gap-2 border-b border-slate-100 pb-2">
                        <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-brand-50 text-brand-600 text-xs font-black">2</span>
                        Tujuan Penggunaan Informasi
                    </h2>
                    <p>Informasi yang terkumpul digunakan untuk:</p>
                    <ul class="list-disc pl-5 space-y-2 text-slate-600">
                        <li>Memproses, memvalidasi, dan mengelola pesanan hewan qurban & aqiqah Anda.</li>
                        <li>Memastikan pelafalan nama shohibul tepat dan syar'i oleh juru sembelih (juleha) bersertifikat.</li>
                        <li>Mengirimkan laporan dan tautan video dokumentasi penyembelihan secara transparan.</li>
                        <li>Mengirimkan notifikasi status transaksi secara real-time ke perangkat Anda.</li>
                        <li>Membantu proses logistik pengiriman olahan daging ke alamat Anda atau ke lokasi distribusi yayasan.</li>
                        <li>Menghubungi Anda perihal kendala administrasi, konfirmasi pembayaran, atau layanan pelanggan.</li>
                    </ul>
                </section>

                <!-- 3. Penyimpanan dan Keamanan Data -->
                <section class="space-y-3">
                    <h2 class="text-base sm:text-lg font-bold text-slate-900 flex items-center gap-2 border-b border-slate-100 pb-2">
                        <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-brand-50 text-brand-600 text-xs font-black">3</span>
                        Penyimpanan dan Keamanan Data
                    </h2>
                    <p>
                        Keamanan data Anda adalah prioritas utama kami. Kami menerapkan standar keamanan teknis dan organisasi yang ketat, termasuk penggunaan enkripsi SSL/TLS (HTTPS) dalam setiap transmisi data, perlindungan basis data bersertifikat, dan kontrol akses berjenjang untuk mencegah kebocoran data, akses tanpa izin, atau modifikasi ilegal.
                    </p>
                </section>

                <!-- 4. Pembagian Data dengan Pihak Ketiga -->
                <section class="space-y-3">
                    <h2 class="text-base sm:text-lg font-bold text-slate-900 flex items-center gap-2 border-b border-slate-100 pb-2">
                        <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-brand-50 text-brand-600 text-xs font-black">4</span>
                        Pemberian Data kepada Pihak Ketiga
                    </h2>
                    <p>
                        Kami tidak menjual, menyewakan, atau memperjualbelikan data pribadi pengguna kepada pihak ketiga mana pun. Data hanya dibagikan kepada mitra operasional resmi yang terikat perjanjian kerahasiaan ketat, antara lain:
                    </p>
                    <ul class="list-disc pl-5 space-y-2 text-slate-600">
                        <li><strong>Payment Gateway Berlisensi (Midtrans):</strong> Untuk memproses transaksi pembayaran secara otomatis dan aman sesuai regulasi Bank Indonesia.</li>
                        <li><strong>Mitra Ekspedisi / Kurir:</strong> Hanya nama, nomor telepon, dan alamat tujuan jika Anda memilih pengiriman olahan daging ke alamat rumah.</li>
                    </ul>
                </section>

                <!-- 5. Hak Pengguna & Penghapusan Akun -->
                <section class="space-y-3">
                    <h2 class="text-base sm:text-lg font-bold text-slate-900 flex items-center gap-2 border-b border-slate-100 pb-2">
                        <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-brand-50 text-brand-600 text-xs font-black">5</span>
                        Hak Anda atas Data & Penghapusan Akun
                    </h2>
                    <p>Sebagai pengguna, Anda memiliki hak penuh untuk:</p>
                    <ul class="list-disc pl-5 space-y-2 text-slate-600">
                        <li>Mengakses, melihat, dan memperbarui informasi profil akun Anda di aplikasi.</li>
                        <li>Mematikan atau mencabut izin push notifications kapan saja melalui pengaturan browser / aplikasi Anda.</li>
                        <li>Meminta penghapusan akun beserta seluruh riwayat data pribadi Anda dengan menghubungi layanan pelanggan resmi kami.</li>
                    </ul>
                </section>

                <!-- 6. Hubungi Kami -->
                <section class="space-y-3 bg-slate-50 rounded-2xl p-5 border border-slate-200">
                    <h2 class="text-sm sm:text-base font-bold text-slate-900">
                        Hubungi Kami
                    </h2>
                    <p class="text-xs sm:text-[13px] text-slate-600">
                        Jika Anda memiliki pertanyaan, saran, atau keluhan terkait Kebijakan Privasi ini, silakan hubungi kami melalui:
                    </p>
                    <div class="space-y-1 text-xs sm:text-[13px] text-slate-800">
                        <p><strong>Entitas Resmi:</strong> PT Delapan Penjuru Piramida</p>
                        <p><strong>Layanan / Brand:</strong> Piramid Qurban & Aqiqah</p>
                        <p><strong>Email:</strong> support@piramid.co.id / admin@piramid.co.id</p>
                        <p><strong>Situs Web:</strong> <a href="/" class="text-brand-600 hover:underline font-semibold">https://piramid.co.id</a></p>
                    </div>
                </section>

                <div class="pt-4 text-center">
                    <Link
                        href="/"
                        class="inline-flex items-center gap-2 rounded-2xl bg-brand-500 hover:bg-brand-600 text-white px-6 py-3 text-xs sm:text-sm font-bold shadow-md transition"
                    >
                        <span>&larr; Kembali ke Halaman Utama</span>
                    </Link>
                </div>
            </div>
        </main>
    </div>
</template>
