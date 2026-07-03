<script setup>
import BrandLogo from '@/Components/BrandLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="flex min-h-screen flex-col items-center bg-brand-50 pt-6 font-sans sm:justify-center sm:pt-0">
        <div>
            <Link href="/" class="flex flex-col items-center gap-1">
                <BrandLogo />
                <span class="font-script text-3xl text-night-soft">Piramid</span>
            </Link>
        </div>

        <div class="mt-6 w-full overflow-hidden bg-white px-6 py-6 shadow-md sm:max-w-md sm:rounded-xl">
            <slot />
        </div>

        <p class="mt-6 pb-8 text-sm text-zinc-500">
            Qurban &amp; Aqiqah Lebih Mudah, Amanah, dan Terjangkau
        </p>
    </div>
</template>
