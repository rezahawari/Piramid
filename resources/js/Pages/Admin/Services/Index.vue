<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

defineProps({
    services: {
        type: Array,
        required: true,
    },
});

const destroy = (service) => {
    if (confirm(`Hapus layanan "${service.name}"?`)) {
        router.delete(route('admin.layanan.destroy', service.id), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Layanan" />

    <AdminLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Layanan</h2>
                <Link
                    :href="route('admin.layanan.create')"
                    class="inline-flex items-center rounded-md bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-gray-700"
                >
                    Tambah Layanan
                </Link>
            </div>
        </template>

        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Slug</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Jumlah Produk</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <tr v-for="service in services" :key="service.id">
                            <td class="px-6 py-4">
                                <p class="text-sm font-medium text-gray-900">{{ service.name }}</p>
                                <p v-if="service.description" class="mt-1 max-w-md truncate text-sm text-gray-500">
                                    {{ service.description }}
                                </p>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ service.slug }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ service.products_count }}</td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <span
                                    class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                                    :class="service.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'"
                                >
                                    {{ service.is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                                <Link
                                    :href="route('admin.layanan.edit', service.id)"
                                    class="font-medium text-brand-600 hover:text-brand-800"
                                >
                                    Ubah
                                </Link>
                                <button
                                    type="button"
                                    class="ml-4 font-medium text-red-600 hover:text-red-800"
                                    @click="destroy(service)"
                                >
                                    Hapus
                                </button>
                            </td>
                        </tr>
                        <tr v-if="services.length === 0">
                            <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">
                                Belum ada layanan.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>
