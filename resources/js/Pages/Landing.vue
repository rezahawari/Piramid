<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';

defineProps({
    services: {
        type: Array,
        default: () => [],
    },
    canLogin: {
        type: Boolean,
        default: true,
    },
    canRegister: {
        type: Boolean,
        default: true,
    },
});

const user = usePage().props.auth?.user;

const placeholderColors = [
    'bg-emerald-600',
    'bg-amber-600',
    'bg-sky-600',
    'bg-rose-600',
];

const placeholderColor = (index) =>
    placeholderColors[index % placeholderColors.length];
</script>

<template>
    <Head title="Beranda" />

    <div class="flex min-h-screen flex-col bg-gray-100">
        <header class="bg-white shadow">
            <div
                class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4 sm:px-6"
            >
                <Link href="/" class="text-xl font-bold text-gray-800">
                    Pyramid
                </Link>

                <nav class="flex items-center gap-4 text-sm">
                    <template v-if="user">
                        <Link
                            href="/transaksi"
                            class="text-gray-600 hover:text-gray-900"
                        >
                            Transaksi Saya
                        </Link>
                        <Link
                            :href="route('dashboard')"
                            class="rounded-md bg-gray-800 px-4 py-2 font-medium text-white hover:bg-gray-700"
                        >
                            Dashboard
                        </Link>
                    </template>
                    <template v-else>
                        <Link
                            v-if="canLogin"
                            :href="route('login')"
                            class="text-gray-600 hover:text-gray-900"
                        >
                            Masuk
                        </Link>
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="rounded-md bg-gray-800 px-4 py-2 font-medium text-white hover:bg-gray-700"
                        >
                            Daftar
                        </Link>
                    </template>
                </nav>
            </div>
        </header>

        <main class="mx-auto w-full max-w-6xl flex-1 px-4 py-10 sm:px-6">
            <h1 class="text-2xl font-bold text-gray-800">Pilih Layanan</h1>
            <p class="mt-1 text-gray-600">
                Salurkan qurban, aqiqah, dan sedekah Anda melalui Pyramid.
            </p>

            <div
                v-if="services.length"
                class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3"
            >
                <div
                    v-for="(service, index) in services"
                    :key="service.id"
                    class="flex flex-col overflow-hidden rounded-lg bg-white shadow"
                >
                    <img
                        v-if="service.cover_image_url"
                        :src="service.cover_image_url"
                        :alt="service.name"
                        class="h-40 w-full object-cover"
                    />
                    <div
                        v-else
                        class="flex h-40 w-full items-center justify-center"
                        :class="placeholderColor(index)"
                    >
                        <span class="text-5xl font-bold text-white">
                            {{ service.name.charAt(0).toUpperCase() }}
                        </span>
                    </div>

                    <div class="flex flex-1 flex-col p-5">
                        <h2 class="text-lg font-semibold text-gray-800">
                            {{ service.name }}
                        </h2>
                        <p class="mt-1 flex-1 text-sm text-gray-600">
                            {{ service.description }}
                        </p>
                        <Link
                            :href="`/layanan/${service.slug}`"
                            class="mt-4 inline-block rounded-md bg-emerald-600 px-4 py-2 text-center text-sm font-medium text-white hover:bg-emerald-700"
                        >
                            Pilih
                        </Link>
                    </div>
                </div>
            </div>

            <p v-else class="mt-8 text-gray-600">
                Belum ada layanan yang tersedia saat ini.
            </p>
        </main>

        <footer class="border-t border-gray-200 bg-white py-4">
            <p class="text-center text-sm text-gray-500">
                &copy; {{ new Date().getFullYear() }} Pyramid — Layanan Qurban,
                Aqiqah, dan Sedekah.
            </p>
        </footer>
    </div>
</template>
