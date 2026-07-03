<script setup>
import BrandLogo from '@/Components/BrandLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <footer class="relative overflow-hidden bg-night text-zinc-400">
        <!-- Watermark kaligrafi samar ala referensi -->
        <span
            class="pointer-events-none absolute -bottom-10 left-8 select-none font-script text-[10rem] leading-none text-white/[0.03]"
            aria-hidden="true"
        >
            Piramid
        </span>

        <div class="relative mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
            <div class="grid gap-10 md:grid-cols-3">
                <div>
                    <BrandLogo variant="full" dark />
                </div>
                <div>
                    <p class="text-sm uppercase tracking-wide text-zinc-500">Quick Navs</p>
                    <div class="mt-3 flex flex-col gap-2 text-lg text-zinc-200">
                        <Link href="/" class="hover:text-brand-300">Beranda</Link>
                        <Link href="/transaksi" class="hover:text-brand-300">Pemesanan</Link>
                    </div>
                </div>
                <div>
                    <p class="text-sm uppercase tracking-wide text-zinc-500">Socials</p>
                    <div class="mt-3 flex flex-col gap-2 text-lg text-zinc-200">
                        <a href="https://instagram.com" target="_blank" rel="noopener" class="hover:text-brand-300">Instagram</a>
                        <a href="https://youtube.com" target="_blank" rel="noopener" class="hover:text-brand-300">Youtube</a>
                    </div>
                </div>
            </div>

            <div class="mt-12 grid gap-8 border-t border-white/10 pt-8 md:grid-cols-3">
                <div class="text-zinc-200">
                    <p>piramid@gmail.com</p>
                    <p>081243124123</p>
                </div>
                <div class="text-sm">
                    JL. Lorem ipsum dolor sit amet consectetur adipisicing elit.
                </div>
                <div class="md:text-right">
                    <a
                        href="https://maps.google.com"
                        target="_blank"
                        rel="noopener"
                        class="font-semibold text-zinc-200 underline underline-offset-4 hover:text-brand-300"
                    >
                        Lihat di Maps ↗
                    </a>
                </div>
            </div>

            <p class="mt-10 border-t border-white/10 pt-6 text-sm">
                Copyright {{ new Date().getFullYear() }} © Piramid. All right reserved.
            </p>
        </div>
    </footer>
</template>
