<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <div class="flex flex-col gap-4 p-6 text-gray-900 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-lg font-semibold">Siap berqurban?</p>
                            <p class="mt-1 text-sm text-gray-600">
                                Pilih hewan qurban &amp; aqiqah, lalu selesaikan pesanan dalam satu langkah.
                            </p>
                        </div>
                        <Link
                            href="/layanan/qurban"
                            class="inline-flex items-center justify-center rounded-md bg-brand-500 px-6 py-3 font-bold text-white transition hover:bg-brand-600"
                        >
                            Mulai Pemesanan
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
