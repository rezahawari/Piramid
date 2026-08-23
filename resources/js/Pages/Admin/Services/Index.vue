<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    services: {
        type: Array,
        required: true,
    },
});

const deleteTarget = ref(null);

const confirmDelete = (service) => {
    deleteTarget.value = service;
};

const executeDelete = () => {
    if (!deleteTarget.value) return;
    router.delete(route('admin.layanan.destroy', deleteTarget.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            deleteTarget.value = null;
        },
    });
};
</script>

<template>
    <Head title="Manajemen Layanan - Admin" />

    <AdminLayout>
        <template #header>
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-bold leading-tight text-gray-900">Manajemen Layanan</h2>
                    <p class="text-xs text-gray-500">Kelola kategori layanan qurban, aqiqah, dan pointing hewan yang tersedia.</p>
                </div>
                <Link
                    :href="route('admin.layanan.create')"
                    class="inline-flex items-center gap-1.5 rounded-xl bg-brand-500 px-4 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-brand-600 self-start sm:self-auto"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Layanan Baru
                </Link>
            </div>
        </template>

        <!-- Modern Services Cards Grid -->
        <div v-if="services.length" class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
            <div
                v-for="service in services"
                :key="service.id"
                class="group flex flex-col justify-between overflow-hidden rounded-2xl border border-gray-200/80 bg-white shadow-sm transition duration-200 hover:-translate-y-1 hover:border-brand-500/40 hover:shadow-md"
            >
                <div>
                    <!-- Cover Image Banner -->
                    <div class="relative h-44 w-full overflow-hidden bg-gray-100">
                        <img
                            v-if="service.cover_image_url"
                            :src="service.cover_image_url"
                            :alt="service.name"
                            class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                        />
                        <div v-else class="flex h-full w-full items-center justify-center bg-gray-100 text-xs text-gray-400">
                            Tanpa Foto Sampul
                        </div>

                        <!-- Status Badge Overlay -->
                        <div class="absolute right-3 top-3">
                            <span
                                class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-bold shadow-sm backdrop-blur-md"
                                :class="service.is_active ? 'bg-emerald-500/90 text-white' : 'bg-gray-700/80 text-gray-200'"
                            >
                                <span class="mr-1.5 h-1.5 w-1.5 rounded-full" :class="service.is_active ? 'bg-white animate-pulse' : 'bg-gray-400'"></span>
                                {{ service.is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>

                        <!-- Slug Tag Overlay -->
                        <div class="absolute bottom-3 left-3">
                            <span class="rounded-lg bg-black/60 px-2.5 py-1 font-mono text-[10px] text-white backdrop-blur-md">
                                /{{ service.slug }}
                            </span>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-5">
                        <div class="flex items-center justify-between gap-2">
                            <h3 class="text-base font-bold text-gray-900 group-hover:text-brand-600 transition">
                                {{ service.name }}
                            </h3>
                            <div class="flex items-center gap-1.5">
                                <span
                                    v-if="service.has_sohibul"
                                    class="shrink-0 rounded-lg bg-amber-50 px-2 py-0.5 text-[10px] font-bold text-amber-700 border border-amber-200"
                                    title="Layanan ini menyertakan form nama sohibul"
                                >
                                    Sohibul Aktif
                                </span>
                                <span class="shrink-0 rounded-lg bg-brand-50 px-2.5 py-1 text-xs font-bold text-brand-700 border border-brand-100">
                                    {{ service.products_count ?? 0 }} Produk
                                </span>
                            </div>
                        </div>

                        <p class="mt-2.5 line-clamp-3 text-xs leading-relaxed text-gray-500">
                            {{ service.description || 'Belum ada deskripsi layanan yang ditambahkan.' }}
                        </p>
                    </div>
                </div>

                <!-- Card Actions -->
                <div class="border-t border-gray-100 bg-gray-50/50 p-4 flex items-center justify-between gap-2">
                    <a
                        :href="`/layanan/${service.slug}`"
                        target="_blank"
                        class="text-xs font-semibold text-gray-500 hover:text-brand-600 transition flex items-center gap-1"
                    >
                        <span>Lihat Publik</span>
                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                    </a>

                    <div class="flex items-center gap-1.5">
                        <Link
                            :href="route('admin.layanan.edit', service.id)"
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
                            @click="confirmDelete(service)"
                        >
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-else class="rounded-3xl border border-dashed border-gray-300 bg-white p-12 text-center shadow-sm">
            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
            <h3 class="mt-3 text-base font-bold text-gray-900">Belum Ada Layanan</h3>
            <p class="mt-1 text-xs text-gray-500">Mulai dengan menambahkan kategori layanan qurban atau aqiqah pertama Anda.</p>
            <Link
                :href="route('admin.layanan.create')"
                class="mt-4 inline-flex items-center gap-1.5 rounded-xl bg-brand-500 px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-brand-600"
            >
                + Tambah Layanan Sekarang
            </Link>
        </div>

        <!-- Modal Konfirmasi Hapus -->
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
                            Hapus Layanan?
                        </h3>
                        <p class="text-xs text-gray-500 font-semibold">{{ deleteTarget.name }}</p>
                    </div>
                </div>

                <p class="text-xs text-gray-600 leading-relaxed">
                    Apakah Anda yakin ingin menghapus layanan ini? Seluruh pointing keterkaitan hewan qurban dengan layanan ini akan terputus.
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
                        Ya, Hapus Layanan
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
