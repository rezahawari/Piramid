<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    stats: {
        type: Object,
        required: true,
    },
});

const cards = [
    { key: 'services', label: 'Layanan', href: '/admin/layanan', color: 'text-indigo-600' },
    { key: 'products', label: 'Produk Hewan', href: '/admin/produk', color: 'text-emerald-600' },
    { key: 'transactions', label: 'Transaksi', href: '/admin/transaksi', color: 'text-blue-600' },
    {
        key: 'pendingManualPayments',
        label: 'Transfer Manual Menunggu Verifikasi',
        href: '/admin/transaksi',
        color: 'text-amber-600',
    },
];
</script>

<template>
    <Head title="Dashboard Admin" />

    <AdminLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Dashboard</h2>
        </template>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <Link
                v-for="card in cards"
                :key="card.key"
                :href="card.href"
                class="rounded-lg bg-white p-6 shadow-sm transition hover:shadow-md"
            >
                <p class="text-sm font-medium text-gray-500">{{ card.label }}</p>
                <p class="mt-2 text-3xl font-bold" :class="card.color">
                    {{ stats[card.key] }}
                </p>
            </Link>
        </div>

        <div class="mt-8 rounded-lg bg-white p-6 shadow-sm">
            <h3 class="text-base font-semibold text-gray-800">Tautan Cepat</h3>
            <div class="mt-4 flex flex-wrap gap-3">
                <Link
                    :href="route('admin.layanan.create')"
                    class="inline-flex items-center rounded-md bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-gray-700"
                >
                    Tambah Layanan
                </Link>
                <Link
                    :href="route('admin.produk.create')"
                    class="inline-flex items-center rounded-md bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-gray-700"
                >
                    Tambah Produk
                </Link>
                <Link
                    :href="route('admin.layanan.index')"
                    class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 hover:bg-gray-50"
                >
                    Kelola Layanan
                </Link>
                <Link
                    :href="route('admin.produk.index')"
                    class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 hover:bg-gray-50"
                >
                    Kelola Produk
                </Link>
            </div>
        </div>
    </AdminLayout>
</template>
