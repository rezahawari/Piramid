<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    service: { type: Object, required: true },
    products: { type: Array, default: () => [] },
});

const page = usePage();
const user = computed(() => page.props.auth?.user ?? null);

const rupiah = (value) =>
    new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(Number(value ?? 0));
</script>

<template>
    <Head :title="`Layanan ${service.name}`" />

    <div class="min-h-screen bg-gray-100">
        <nav class="border-b border-gray-100 bg-white">
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <Link href="/" class="flex items-center gap-2">
                    <ApplicationLogo class="block h-9 w-auto fill-current text-gray-800" />
                    <span class="font-semibold text-gray-800">Pyramid</span>
                </Link>
                <div class="flex items-center gap-4 text-sm font-medium">
                    <template v-if="user">
                        <Link :href="route('dashboard')" class="text-gray-600 hover:text-gray-900">Dashboard</Link>
                        <Link :href="route('transactions.index')" class="text-gray-600 hover:text-gray-900">Transaksi Saya</Link>
                    </template>
                    <template v-else>
                        <Link :href="route('login')" class="text-gray-600 hover:text-gray-900">Masuk</Link>
                        <Link :href="route('register')" class="text-gray-600 hover:text-gray-900">Daftar</Link>
                    </template>
                </div>
            </div>
        </nav>

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
                                    class="inline-flex w-full items-center justify-center rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white transition hover:bg-gray-700"
                                >
                                    Lihat Detail
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
    </div>
</template>
