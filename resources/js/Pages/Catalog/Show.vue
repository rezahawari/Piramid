<script setup>
import PublicFooter from '@/Components/PublicFooter.vue';
import PublicNavbar from '@/Components/PublicNavbar.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    service: { type: Object, required: true },
    product: { type: Object, required: true },
});

const rupiah = (value) =>
    new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(Number(value ?? 0));

const inStock = computed(() => props.product.stock > 0);
</script>

<template>
    <Head :title="`${product.name} — ${service.name}`" />

    <div class="min-h-screen bg-brand-50 font-sans">
        <PublicNavbar />

        <main class="py-12">
            <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
                <nav class="mb-6 text-sm text-gray-500">
                    <Link
                        :href="route('catalog.index', { service: service.slug })"
                        class="hover:text-gray-700 hover:underline"
                    >
                        Layanan {{ service.name }}
                    </Link>
                    <span class="mx-1">/</span>
                    <span class="text-gray-800">{{ product.name }}</span>
                </nav>

                <div class="overflow-hidden rounded-lg bg-white shadow-sm md:grid md:grid-cols-2">
                    <div>
                        <img
                            v-if="product.primary_image_url"
                            :src="product.primary_image_url"
                            :alt="product.name"
                            class="h-72 w-full object-cover md:h-full"
                        />
                        <div
                            v-else
                            class="flex h-72 w-full items-center justify-center bg-gray-200 text-sm text-gray-400 md:h-full"
                        >
                            Tidak ada gambar
                        </div>
                    </div>

                    <div class="p-6 md:p-8">
                        <h1 class="text-2xl font-bold text-gray-900">{{ product.name }}</h1>
                        <p class="mt-1 text-2xl font-semibold text-gray-800">
                            {{ rupiah(product.price) }}
                        </p>

                        <dl class="mt-4 space-y-2 border-t border-gray-100 pt-4 text-sm text-gray-600">
                            <div v-if="product.weight_estimate_kg" class="flex justify-between">
                                <dt>Estimasi bobot</dt>
                                <dd class="font-medium text-gray-800">
                                    {{ Number(product.weight_estimate_kg) }} kg
                                </dd>
                            </div>
                            <div class="flex justify-between">
                                <dt>Stok</dt>
                                <dd
                                    class="font-medium"
                                    :class="inStock ? 'text-green-700' : 'text-red-600'"
                                >
                                    {{ inStock ? `${product.stock} tersedia` : 'Habis' }}
                                </dd>
                            </div>
                        </dl>

                        <p v-if="product.description" class="mt-4 whitespace-pre-line text-sm leading-relaxed text-gray-600">
                            {{ product.description }}
                        </p>

                        <div class="mt-8">
                            <Link
                                v-if="inStock"
                                :href="route('checkout.create', { service: service.slug, product: product.slug })"
                                class="inline-flex w-full items-center justify-center rounded-md bg-sun-400 px-6 py-3 text-sm font-bold uppercase tracking-widest text-cocoa transition hover:bg-sun-500"
                            >
                                Pesan Sekarang
                            </Link>
                            <button
                                v-else
                                type="button"
                                disabled
                                class="inline-flex w-full cursor-not-allowed items-center justify-center rounded-md bg-gray-300 px-6 py-3 text-sm font-semibold uppercase tracking-widest text-gray-500"
                            >
                                Stok Habis
                            </button>
                            <p v-if="!$page.props.auth.user && inStock" class="mt-2 text-center text-xs text-gray-500">
                                Anda akan diminta masuk terlebih dahulu sebelum memesan.
                            </p>
                        </div>
                    </div>
                </div>

                <div v-if="product.gallery?.length" class="mt-8">
                    <h2 class="mb-3 text-lg font-semibold text-gray-800">Galeri</h2>
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4">
                        <a
                            v-for="(imageUrl, i) in product.gallery"
                            :key="i"
                            :href="imageUrl"
                            target="_blank"
                            rel="noopener"
                            class="overflow-hidden rounded-lg border bg-white"
                        >
                            <img
                                :src="imageUrl"
                                :alt="`${product.name} ${i + 1}`"
                                class="h-32 w-full object-cover"
                                loading="lazy"
                            />
                        </a>
                    </div>
                </div>
            </div>
        </main>

        <PublicFooter />
    </div>
</template>
