<script setup>
import PublicFooter from '@/Components/PublicFooter.vue';
import PublicNavbar from '@/Components/PublicNavbar.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    service: { type: Object, required: true },
    products: { type: Array, default: () => [] },
});

const rupiah = (value) =>
    new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(Number(value ?? 0));
</script>

<template>
    <Head :title="`Layanan ${service.name}`" />

    <div class="min-h-screen bg-brand-50 font-sans">
        <PublicNavbar />

        <header class="bg-white shadow">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <h1 class="text-xl font-semibold leading-tight text-gray-800">
                    Layanan {{ service.name }}
                </h1>
                <p v-if="service.description" class="mt-1 text-sm text-gray-600">
                    {{ service.description }}
                </p>
            </div>
        </header>

        <main class="py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div
                    v-if="products.length"
                    class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3"
                >
                    <div
                        v-for="product in products"
                        :key="product.id"
                        class="flex flex-col overflow-hidden rounded-lg bg-white shadow-sm"
                    >
                        <img
                            v-if="product.primary_image_url"
                            :src="product.primary_image_url"
                            :alt="product.name"
                            class="h-48 w-full object-cover"
                            loading="lazy"
                        />
                        <div
                            v-else
                            class="flex h-48 w-full items-center justify-center bg-gray-200 text-sm text-gray-400"
                        >
                            Tidak ada gambar
                        </div>

                        <div class="flex flex-1 flex-col p-4">
                            <h2 class="text-lg font-semibold text-gray-800">{{ product.name }}</h2>
                            <p class="mt-1 line-clamp-2 text-sm text-gray-500">
                                {{ product.description }}
                            </p>

                            <dl class="mt-3 space-y-1 text-sm text-gray-600">
                                <div class="flex justify-between">
                                    <dt>Harga</dt>
                                    <dd class="font-semibold text-gray-900">{{ rupiah(product.price) }}</dd>
                                </div>
                                <div v-if="product.weight_estimate_kg" class="flex justify-between">
                                    <dt>Estimasi bobot</dt>
                                    <dd>{{ Number(product.weight_estimate_kg) }} kg</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt>Stok</dt>
                                    <dd :class="product.stock > 0 ? 'text-green-700' : 'text-red-600'">
                                        {{ product.stock > 0 ? `${product.stock} tersedia` : 'Habis' }}
                                    </dd>
                                </div>
                            </dl>

                            <div class="mt-4 pt-2">
                                <Link
                                    :href="route('catalog.show', { service: service.slug, product: product.slug })"
                                    class="inline-flex w-full items-center justify-center rounded-md bg-sun-400 px-4 py-2 text-sm font-bold text-cocoa transition hover:bg-sun-500"
                                >
                                    Pesan Sekarang
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else class="rounded-lg bg-white p-8 text-center text-gray-500 shadow-sm">
                    Belum ada produk tersedia untuk layanan ini.
                </div>
            </div>
        </main>

        <PublicFooter />
    </div>
</template>
