<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

defineProps({
    products: {
        type: Array,
        required: true,
    },
});

const formatRupiah = (value) =>
    new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(Number(value ?? 0));

const destroy = (product) => {
    if (confirm(`Hapus produk "${product.name}"?`)) {
        router.delete(route('admin.produk.destroy', product.id), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Produk Hewan" />

    <AdminLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Produk Hewan</h2>
                <Link
                    :href="route('admin.produk.create')"
                    class="inline-flex items-center rounded-md bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-gray-700"
                >
                    Tambah Produk
                </Link>
            </div>
        </template>

        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Harga</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Stok</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Layanan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <tr v-for="product in products" :key="product.id">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <img
                                        v-if="product.primary_image_url"
                                        :src="product.primary_image_url"
                                        :alt="product.name"
                                        class="h-10 w-10 rounded object-cover"
                                    />
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ product.name }}</p>
                                        <p class="text-sm text-gray-500">{{ product.slug }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                {{ formatRupiah(product.price) }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ product.stock }}</td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    <span
                                        v-for="service in product.services"
                                        :key="service.id"
                                        class="inline-flex items-center rounded-full bg-indigo-100 px-2.5 py-0.5 text-xs font-medium text-indigo-700"
                                    >
                                        {{ service.name }}
                                    </span>
                                    <span
                                        v-if="product.services.length === 0"
                                        class="text-xs text-gray-400"
                                    >
                                        —
                                    </span>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <span
                                    class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                                    :class="product.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'"
                                >
                                    {{ product.is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                                <Link
                                    :href="route('admin.produk.edit', product.id)"
                                    class="font-medium text-indigo-600 hover:text-indigo-800"
                                >
                                    Ubah
                                </Link>
                                <button
                                    type="button"
                                    class="ml-4 font-medium text-red-600 hover:text-red-800"
                                    @click="destroy(product)"
                                >
                                    Hapus
                                </button>
                            </td>
                        </tr>
                        <tr v-if="products.length === 0">
                            <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">
                                Belum ada produk.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>
