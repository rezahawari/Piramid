<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
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

const deleteTarget = ref(null);

const confirmDelete = (product) => {
    deleteTarget.value = product;
};

const executeDelete = () => {
    if (!deleteTarget.value) return;
    router.delete(route('admin.produk.destroy', deleteTarget.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            deleteTarget.value = null;
        },
    });
};
</script>

<template>
    <Head title="Manajemen Produk Hewan - Admin" />

    <AdminLayout>
        <!-- ================= MOBILE APP NATIVE TOP HEADER ================= -->
        <div class="block md:hidden bg-gradient-to-b from-slate-900 via-zinc-900 to-zinc-900 text-white pt-4 pb-6 px-4 -mx-3 -mt-4 rounded-b-[2rem] shadow-xl relative overflow-hidden mb-5">
            <div class="flex items-center justify-between relative z-10">
                <div>
                    <span class="text-[10px] text-zinc-400 font-bold uppercase tracking-wider block">Katalog Hewan</span>
                    <h2 class="text-base font-black text-white leading-tight">Kelola Produk</h2>
                </div>
                <Link
                    :href="route('admin.produk.create')"
                    class="inline-flex items-center gap-1 rounded-xl bg-brand-500 hover:bg-brand-600 px-3 py-1.5 text-xs font-bold text-white shadow-xs"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah</span>
                </Link>
            </div>
        </div>

        <template #header>
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-bold leading-tight text-gray-900">Katalog Produk Hewan</h2>
                    <p class="text-xs text-gray-500">Kelola stok hewan qurban, aqiqah, estimasi bobot, dan penetapan harga.</p>
                </div>
                <Link
                    :href="route('admin.produk.create')"
                    class="inline-flex items-center gap-1.5 rounded-xl bg-brand-500 px-4 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-brand-600 self-start sm:self-auto"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Hewan Baru
                </Link>
            </div>
        </template>

        <!-- ================= MOBILE VIEW: NATIVE CARDS ================= -->
        <div class="block md:hidden space-y-3 mb-6">
            <div
                v-for="product in products"
                :key="product.id"
                class="rounded-2xl border border-gray-200/80 bg-white p-4 shadow-xs space-y-3"
            >
                <div class="flex gap-3.5">
                    <img
                        v-if="product.primary_image_url"
                        :src="product.primary_image_url"
                        :alt="product.name"
                        class="h-16 w-16 rounded-2xl object-cover border border-gray-100 shrink-0"
                    />
                    <div v-else class="h-16 w-16 rounded-2xl bg-gray-100 flex items-center justify-center text-xs text-gray-400 shrink-0">
                        No Pic
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-1">
                            <h4 class="text-sm font-bold text-gray-900 truncate">{{ product.name }}</h4>
                            <span
                                class="inline-flex items-center rounded-lg px-2 py-0.5 text-[10px] font-bold"
                                :class="product.is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-600'"
                            >
                                {{ product.is_active ? 'Aktif' : 'Draft' }}
                            </span>
                        </div>
                        <p class="text-sm font-black text-brand-600 mt-0.5">{{ formatRupiah(product.price) }}</p>
                        <div class="flex flex-wrap items-center gap-1.5 mt-1 text-[11px]">
                            <span class="text-zinc-500 font-medium">Stok: <strong>{{ product.stock }} Ekor</strong></span>
                            <span v-if="product.weight_estimate_kg" class="text-zinc-400">· ~{{ product.weight_estimate_kg }} Kg</span>
                        </div>
                    </div>
                </div>

                <div class="pt-2 border-t border-gray-100 flex items-center justify-between gap-2">
                    <div class="flex flex-wrap gap-1">
                        <span
                            v-for="s in product.services"
                            :key="s.id"
                            class="rounded bg-brand-50 px-2 py-0.5 text-[10px] font-bold text-brand-700"
                        >
                            {{ s.name }}
                        </span>
                    </div>

                    <div class="flex items-center gap-1.5">
                        <Link
                            :href="route('admin.produk.edit', product.id)"
                            class="rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1.5 text-xs font-bold transition"
                        >
                            Edit
                        </Link>
                        <button
                            type="button"
                            @click="confirmDelete(product)"
                            class="rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 px-2.5 py-1.5 text-xs font-bold transition"
                        >
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= DESKTOP VIEW: TABLE CARD ================= -->
        <div class="hidden md:block overflow-hidden rounded-2xl border border-gray-200/80 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="border-b border-gray-200 bg-gray-50/75 text-[11px] font-bold uppercase tracking-wider text-gray-500">
                        <tr>
                            <th class="px-5 py-3.5">Produk Hewan</th>
                            <th class="px-5 py-3.5">Harga</th>
                            <th class="px-5 py-3.5">Bobot</th>
                            <th class="px-5 py-3.5">Stok</th>
                            <th class="px-5 py-3.5">Maks. Sohibul</th>
                            <th class="px-5 py-3.5">Layanan</th>
                            <th class="px-5 py-3.5">Status</th>
                            <th class="px-5 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr
                            v-for="product in products"
                            :key="product.id"
                            class="hover:bg-gray-50/75 transition duration-150"
                        >
                            <!-- Nama Hewan & Gambar -->
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3.5">
                                    <img
                                        v-if="product.primary_image_url"
                                        :src="product.primary_image_url"
                                        :alt="product.name"
                                        class="h-12 w-12 shrink-0 rounded-xl object-cover border border-gray-100 shadow-xs"
                                    />
                                    <div v-else class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gray-100 text-xs text-gray-400">
                                        No Pic
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900 hover:text-brand-600 transition">{{ product.name }}</p>
                                        <p class="font-mono text-[11px] text-gray-400 mt-0.5">/{{ product.slug }}</p>
                                    </div>
                                </div>
                            </td>

                            <!-- Harga -->
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="font-black text-brand-600 text-sm">
                                    {{ formatRupiah(product.price) }}
                                </span>
                            </td>

                            <!-- Estimasi Bobot -->
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span v-if="product.weight_estimate_kg" class="font-medium text-gray-700">
                                    ~{{ product.weight_estimate_kg }} kg
                                </span>
                                <span v-else class="text-gray-400">—</span>
                            </td>

                            <!-- Stok -->
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span
                                    class="inline-flex items-center rounded-lg px-2.5 py-1 text-xs font-bold"
                                    :class="
                                        product.stock <= 0
                                            ? 'bg-rose-50 text-rose-700'
                                            : product.stock <= 5
                                            ? 'bg-amber-50 text-amber-700'
                                            : 'bg-emerald-50 text-emerald-700'
                                    "
                                >
                                    {{ product.stock }} Ekor
                                </span>
                            </td>

                            <!-- Maks. Sohibul -->
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center rounded-lg bg-indigo-50 border border-indigo-100 px-2 py-0.5 text-xs font-bold text-indigo-700">
                                    {{ product.max_sohibul ?? 1 }} Orang / Ekor
                                </span>
                            </td>

                            <!-- Layanan Pointing -->
                            <td class="px-5 py-4">
                                <div class="flex flex-wrap gap-1">
                                    <span
                                        v-for="service in product.services"
                                        :key="service.id"
                                        class="inline-flex items-center rounded-lg bg-brand-50 border border-brand-100 px-2 py-0.5 text-[11px] font-semibold text-brand-700"
                                    >
                                        {{ service.name }}
                                    </span>
                                    <span v-if="product.services?.length === 0" class="text-gray-400">
                                        Belum ditautkan
                                    </span>
                                </div>
                            </td>

                            <!-- Status Aktif -->
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span
                                    class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[11px] font-bold"
                                    :class="product.is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-gray-100 text-gray-500'"
                                >
                                    <span class="mr-1.5 h-1.5 w-1.5 rounded-full" :class="product.is_active ? 'bg-emerald-500' : 'bg-gray-400'"></span>
                                    {{ product.is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>

                            <!-- Aksi -->
                            <td class="px-5 py-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <Link
                                        :href="route('admin.produk.edit', product.id)"
                                        class="inline-flex items-center gap-1 rounded-xl bg-white border border-gray-200 px-3 py-1.5 text-xs font-bold text-gray-700 shadow-sm transition hover:bg-brand-50 hover:text-brand-700 hover:border-brand-200"
                                    >
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Ubah
                                    </Link>
                                    <button
                                        type="button"
                                        class="inline-flex items-center gap-1 rounded-xl bg-white border border-gray-200 px-3 py-1.5 text-xs font-bold text-rose-600 shadow-sm transition hover:bg-rose-50 hover:border-rose-200"
                                        @click="confirmDelete(product)"
                                    >
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="products.length === 0">
                            <td colspan="7" class="px-5 py-12 text-center text-gray-400">
                                <svg class="mx-auto h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                <p class="mt-2 text-sm font-semibold text-gray-700">Belum ada produk hewan.</p>
                                <p class="text-xs text-gray-400">Tambahkan jenis kambing, domba, atau sapi pertama Anda.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Konfirmasi Hapus Produk -->
        <div
            v-if="deleteTarget"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm"
            @click.self="deleteTarget = null"
        >
            <div class="w-full max-w-md space-y-4 rounded-2xl bg-white p-6 shadow-2xl">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-rose-100 text-rose-700">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900">
                            Hapus Produk Hewan?
                        </h3>
                        <p class="text-xs text-gray-500 font-semibold">{{ deleteTarget.name }}</p>
                    </div>
                </div>

                <p class="text-xs text-gray-600 leading-relaxed">
                    Apakah Anda yakin ingin menghapus data hewan ini? Data produk tidak dapat dipulihkan setelah dihapus.
                </p>

                <div class="flex justify-end gap-2 border-t border-gray-100 pt-3">
                    <button
                        type="button"
                        class="rounded-xl border border-gray-300 bg-white px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50"
                        @click="deleteTarget = null"
                    >
                        Batal
                    </button>
                    <button
                        type="button"
                        class="rounded-xl bg-rose-600 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-rose-700"
                        @click="executeDelete"
                    >
                        Ya, Hapus Produk
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
