<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    items: {
        type: Array,
        required: true,
    },
});

const page = usePage();
const deleteTarget = ref(null);

const confirmDelete = (item) => {
    deleteTarget.value = item;
};

const executeDelete = () => {
    if (!deleteTarget.value) return;
    router.delete(route('admin.galeri.destroy', deleteTarget.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            deleteTarget.value = null;
        },
    });
};
</script>

<template>
    <Head title="Galeri Dokumentasi & Edukasi - Admin" />

    <AdminLayout>
        <template #header>
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-bold leading-tight text-gray-900">Galeri Dokumentasi & Edukasi</h2>
                    <p class="text-xs text-gray-500">Kelola koleksi foto, video lokal, dan video YouTube yang tampil di landing page publik.</p>
                </div>
                <Link
                    :href="route('admin.galeri.create')"
                    class="inline-flex items-center gap-1.5 rounded-xl bg-brand-500 px-4 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-brand-600 self-start sm:self-auto"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Media Baru
                </Link>
            </div>
        </template>

        <!-- Flash Notice -->
        <div
            v-if="page.props.flash?.success"
            class="mb-6 flex items-center gap-3 rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-sm text-emerald-800 shadow-sm"
        >
            <svg class="h-5 w-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span>{{ page.props.flash.success }}</span>
        </div>

        <!-- Media Cards Grid -->
        <div v-if="items.length" class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
            <div
                v-for="item in items"
                :key="item.id"
                class="group flex flex-col justify-between overflow-hidden rounded-2xl border border-gray-200/80 bg-white shadow-sm transition duration-200 hover:-translate-y-1 hover:border-brand-500/40 hover:shadow-md"
            >
                <div>
                    <!-- Media Preview Area -->
                    <div class="relative h-48 w-full overflow-hidden bg-black/90">
                        <!-- Image -->
                        <img
                            v-if="item.type === 'image' && item.file_url"
                            :src="item.file_url"
                            :alt="item.title"
                            class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                        />

                        <!-- Video Local -->
                        <video
                            v-else-if="item.type === 'video' && item.file_url"
                            :src="item.file_url"
                            class="h-full w-full object-cover"
                            controls
                        ></video>

                        <!-- YouTube Embed Preview -->
                        <div v-else-if="item.type === 'youtube' && item.youtube_url" class="relative h-full w-full">
                            <iframe
                                :src="item.youtube_url"
                                class="h-full w-full"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen
                            ></iframe>
                        </div>

                        <div v-else class="flex h-full w-full items-center justify-center text-xs text-gray-400">
                            Tanpa Pratinjau Media
                        </div>

                        <!-- Type Badge -->
                        <div class="absolute left-3 top-3">
                            <span class="rounded-lg bg-black/70 px-2.5 py-1 text-[10px] font-bold uppercase text-white backdrop-blur-md">
                                {{ item.type === 'youtube' ? '▶ YouTube' : (item.type === 'video' ? '🎬 Video' : '📷 Foto') }}
                            </span>
                        </div>

                        <!-- Status Badge -->
                        <div class="absolute right-3 top-3">
                            <span
                                class="inline-flex items-center rounded-full px-2.5 py-1 text-[10px] font-bold shadow-sm backdrop-blur-md"
                                :class="item.is_active ? 'bg-emerald-500/90 text-white' : 'bg-gray-700/80 text-gray-200'"
                            >
                                {{ item.is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>
                    </div>

                    <!-- Content Details -->
                    <div class="p-5">
                        <div class="flex items-center justify-between gap-2">
                            <span class="rounded-lg bg-brand-50 border border-brand-100 px-2 py-0.5 text-[10px] font-bold uppercase text-brand-700">
                                {{ item.category }}
                            </span>
                            <span class="text-[11px] text-gray-400">Urutan: #{{ item.order_index }}</span>
                        </div>

                        <h3 class="mt-2.5 text-base font-bold text-gray-900 group-hover:text-brand-600 transition line-clamp-1">
                            {{ item.title }}
                        </h3>

                        <p class="mt-1.5 line-clamp-2 text-xs text-gray-500 leading-relaxed">
                            {{ item.description || 'Tidak ada deskripsi media.' }}
                        </p>
                    </div>
                </div>

                <!-- Card Actions -->
                <div class="border-t border-gray-100 bg-gray-50/50 p-4 flex items-center justify-end gap-2">
                    <Link
                        :href="route('admin.galeri.edit', item.id)"
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
                        @click="confirmDelete(item)"
                    >
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Hapus
                    </button>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-else class="rounded-3xl border border-dashed border-gray-300 bg-white p-12 text-center shadow-sm">
            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <h3 class="mt-3 text-base font-bold text-gray-900">Belum Ada Media Dokumentasi</h3>
            <p class="mt-1 text-xs text-gray-500">Unggah foto pemotongan syari, video distribusi, atau link YouTube edukasi.</p>
            <Link
                :href="route('admin.galeri.create')"
                class="mt-4 inline-flex items-center gap-1.5 rounded-xl bg-brand-500 px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-brand-600"
            >
                + Tambah Media Sekarang
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
                            Hapus Media Dokumentasi?
                        </h3>
                        <p class="text-xs text-gray-500 font-semibold">{{ deleteTarget.title }}</p>
                    </div>
                </div>

                <p class="text-xs text-gray-600 leading-relaxed">
                    Apakah Anda yakin ingin menghapus media ini? Media tidak akan ditampilkan lagi di galeri landing page publik.
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
                        Ya, Hapus Media
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
