<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    legalities: {
        type: Array,
        required: true,
    },
});

const page = usePage();
const deleteTarget = ref(null);
const previewModal = ref(null);

const confirmDelete = (item) => {
    deleteTarget.value = item;
};

const executeDelete = () => {
    if (!deleteTarget.value) return;
    router.delete(route('admin.legalities.destroy', deleteTarget.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            deleteTarget.value = null;
        },
    });
};

const toggleStatus = (item) => {
    router.patch(route('admin.legalities.toggle-status', item.id), {}, {
        preserveScroll: true,
    });
};

const isPdf = (item) => {
    return item.file_type === 'pdf' || (item.file_path && item.file_path.toLowerCase().endsWith('.pdf'));
};
</script>

<template>
    <Head title="Manajemen Legalitas - Admin" />

    <AdminLayout>
        <template #header>
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-bold leading-tight text-gray-900">CMS Legalitas Perusahaan &amp; Syariat</h2>
                    <p class="text-xs text-gray-500">Kelola berkas legalitas, perizinan, sertifikasi halal/RPH, akta yayasan, dan dokumen syariat (PDF, PNG, JPG).</p>
                </div>
                <Link
                    :href="route('admin.legalities.create')"
                    class="inline-flex items-center gap-1.5 rounded-xl bg-brand-500 px-4 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-brand-600 self-start sm:self-auto cursor-pointer"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Dokumen Legalitas
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

        <!-- Empty State -->
        <div
            v-if="!legalities || legalities.length === 0"
            class="rounded-3xl border-2 border-dashed border-gray-200 bg-white p-12 text-center"
        >
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-50 text-amber-500">
                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <h3 class="mt-4 text-base font-bold text-gray-900">Belum ada dokumen legalitas</h3>
            <p class="mt-1 text-xs text-gray-500 max-w-md mx-auto">
                Silakan tambahkan dokumen legalitas seperti izin operasional, sertifikat halal MUI, nomor induk berusaha (NIB), atau SK Kemenkumham.
            </p>
            <div class="mt-6">
                <Link
                    :href="route('admin.legalities.create')"
                    class="inline-flex items-center gap-1.5 rounded-xl bg-brand-500 px-4 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-brand-600 transition"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Dokumen Pertama
                </Link>
            </div>
        </div>

        <!-- Cards Grid -->
        <div v-else class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
            <div
                v-for="item in legalities"
                :key="item.id"
                class="group flex flex-col justify-between overflow-hidden rounded-3xl border border-gray-200/90 bg-white shadow-xs transition duration-200 hover:-translate-y-1 hover:border-brand-500/40 hover:shadow-md"
            >
                <div>
                    <!-- Preview Area -->
                    <div class="relative h-44 w-full overflow-hidden bg-slate-900 border-b border-gray-100 flex items-center justify-center">
                        <!-- Image File Preview -->
                        <img
                            v-if="!isPdf(item) && item.file_path"
                            :src="item.file_path"
                            :alt="item.title"
                            class="h-full w-full object-cover transition duration-300 group-hover:scale-105 cursor-pointer"
                            @click="previewModal = item"
                        />

                        <!-- PDF Placeholder with Icon -->
                        <div
                            v-else
                            class="flex flex-col items-center justify-center gap-2 p-6 text-center cursor-pointer w-full h-full bg-linear-to-br from-slate-900 via-slate-800 to-slate-950 text-white"
                            @click="previewModal = item"
                        >
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-500/20 text-rose-400 border border-rose-500/30">
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zM6 4h7v5h5v11H6V4zm8 9a1 1 0 0 1 1 1v1h1a1 1 0 1 1 0 2h-1v1a1 1 0 1 1-2 0v-4a1 1 0 0 1 1-1z" />
                                </svg>
                            </div>
                            <span class="text-xs font-bold text-white tracking-wide">DOKUMEN PDF RESMI</span>
                            <span class="text-[10px] text-zinc-400">Klik untuk melihat pratinjau</span>
                        </div>

                        <!-- Top Floating Badges -->
                        <div class="absolute top-3 inset-x-3 flex items-center justify-between pointer-events-none">
                            <span
                                class="rounded-lg px-2.5 py-1 text-[10px] font-black uppercase tracking-wider text-white shadow-xs backdrop-blur-md"
                                :class="isPdf(item) ? 'bg-rose-600/90' : 'bg-blue-600/90'"
                            >
                                {{ isPdf(item) ? 'PDF' : item.file_type.toUpperCase() }}
                            </span>

                            <button
                                type="button"
                                @click.stop="toggleStatus(item)"
                                class="pointer-events-auto rounded-lg px-2.5 py-1 text-[10px] font-bold shadow-xs backdrop-blur-md transition cursor-pointer"
                                :class="item.is_active ? 'bg-emerald-600/90 text-white hover:bg-emerald-700' : 'bg-zinc-800/90 text-zinc-300 hover:bg-zinc-900'"
                            >
                                {{ item.is_active ? '● Aktif' : '○ Nonaktif' }}
                            </button>
                        </div>

                        <!-- Bottom Size & Date Pill -->
                        <div v-if="item.file_size" class="absolute bottom-2 right-2 rounded-md bg-black/60 px-2 py-0.5 text-[10px] font-semibold text-zinc-200 backdrop-blur-xs">
                            {{ item.file_size }}
                        </div>
                    </div>

                    <!-- Details Area -->
                    <div class="p-5">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <span v-if="item.issuer" class="text-[10px] font-bold uppercase tracking-wider text-brand-600">
                                    {{ item.issuer }}
                                </span>
                                <h3 class="text-sm font-bold text-gray-900 line-clamp-1">
                                    {{ item.title }}
                                </h3>
                            </div>
                            <span class="rounded-md bg-gray-100 px-2 py-0.5 text-[10px] font-semibold text-gray-600 shrink-0">
                                Urutan #{{ item.order }}
                            </span>
                        </div>

                        <div v-if="item.document_number" class="mt-2 flex items-center gap-1.5 text-xs text-gray-600 font-mono bg-gray-50 px-2.5 py-1.5 rounded-xl border border-gray-100">
                            <svg class="h-3.5 w-3.5 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                            </svg>
                            <span class="truncate">{{ item.document_number }}</span>
                        </div>

                        <p v-if="item.description" class="mt-2.5 text-xs text-gray-500 line-clamp-2 leading-relaxed">
                            {{ item.description }}
                        </p>

                        <div v-if="item.issued_date" class="mt-3 flex items-center gap-1 text-[11px] text-gray-400">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>Terbit: {{ item.issued_date }}</span>
                        </div>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="flex items-center justify-between border-t border-gray-100 bg-gray-50/70 px-5 py-3">
                    <a
                        :href="item.file_path"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center gap-1 text-xs font-bold text-zinc-700 hover:text-brand-600 transition"
                    >
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                        Buka File
                    </a>

                    <div class="flex items-center gap-2">
                        <Link
                            :href="route('admin.legalities.edit', item.id)"
                            class="inline-flex items-center gap-1 rounded-xl bg-white border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-100 hover:text-gray-900 transition shadow-2xs"
                        >
                            Edit
                        </Link>
                        <button
                            type="button"
                            @click="confirmDelete(item)"
                            class="inline-flex items-center gap-1 rounded-xl bg-rose-50 border border-rose-200 px-3 py-1.5 text-xs font-semibold text-rose-600 hover:bg-rose-100 transition shadow-2xs cursor-pointer"
                        >
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Pratinjau Dokumen -->
        <div
            v-if="previewModal"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
            @click.self="previewModal = null"
        >
            <div class="relative w-full max-w-4xl max-h-[90vh] flex flex-col rounded-3xl bg-white shadow-2xl overflow-hidden">
                <!-- Modal Header -->
                <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4 bg-gray-50/80">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">{{ previewModal.title }}</h3>
                        <p class="text-xs text-gray-500 font-mono">{{ previewModal.document_number || 'Tanpa nomor dokumen' }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <a
                            :href="previewModal.file_path"
                            download
                            target="_blank"
                            class="inline-flex items-center gap-1.5 rounded-xl bg-brand-500 px-3 py-1.5 text-xs font-bold text-white hover:bg-brand-600 transition"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Unduh File
                        </a>
                        <button
                            type="button"
                            @click="previewModal = null"
                            class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-300 hover:text-gray-800 transition"
                        >
                            ✕
                        </button>
                    </div>
                </div>

                <!-- Modal Body Preview -->
                <div class="flex-1 overflow-auto bg-slate-950 p-4 flex items-center justify-center min-h-[400px]">
                    <iframe
                        v-if="isPdf(previewModal)"
                        :src="previewModal.file_path"
                        class="w-full h-[70vh] rounded-xl border border-white/10"
                    ></iframe>
                    <img
                        v-else
                        :src="previewModal.file_path"
                        :alt="previewModal.title"
                        class="max-h-[75vh] max-w-full rounded-xl object-contain shadow-md"
                    />
                </div>
            </div>
        </div>

        <!-- Modal Konfirmasi Hapus -->
        <div
            v-if="deleteTarget"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
            @click.self="deleteTarget = null"
        >
            <div class="w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-100 text-rose-600 mx-auto">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <div class="mt-4 text-center">
                    <h3 class="text-base font-bold text-gray-900">Hapus Dokumen Legalitas?</h3>
                    <p class="mt-2 text-xs text-gray-500">
                        Apakah Anda yakin ingin menghapus dokumen <strong>"{{ deleteTarget.title }}"</strong>? Berkas ini tidak akan dapat diakses kembali oleh pengunjung web.
                    </p>
                </div>
                <div class="mt-6 flex items-center justify-end gap-3">
                    <button
                        type="button"
                        @click="deleteTarget = null"
                        class="rounded-xl bg-gray-100 px-4 py-2 text-xs font-bold text-gray-700 hover:bg-gray-200 transition"
                    >
                        Batal
                    </button>
                    <button
                        type="button"
                        @click="executeDelete"
                        class="rounded-xl bg-rose-600 px-4 py-2 text-xs font-bold text-white hover:bg-rose-700 transition"
                    >
                        Ya, Hapus Dokumen
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
