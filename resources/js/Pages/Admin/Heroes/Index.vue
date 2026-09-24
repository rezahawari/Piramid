<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    heroes: {
        type: Array,
        required: true,
    },
});

const page = usePage();
const deleteTarget = ref(null);

const confirmDelete = (hero) => {
    deleteTarget.value = hero;
};

const executeDelete = () => {
    if (!deleteTarget.value) return;
    router.delete(route('admin.heroes.destroy', deleteTarget.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            deleteTarget.value = null;
        },
    });
};

const toggleStatus = (hero) => {
    router.patch(route('admin.heroes.toggle-status', hero.id), {}, {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="CMS Landing Hero & Onboarding - Admin" />

    <AdminLayout>
        <template #header>
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-bold leading-tight text-gray-900">CMS Landing Hero & Onboarding Mobile</h2>
                    <p class="text-xs text-gray-500">Kelola slider hero banner beranda web dan slide onboarding aplikasi mobile native secara dinamis.</p>
                </div>
                <Link
                    :href="route('admin.heroes.create')"
                    class="inline-flex items-center gap-1.5 rounded-xl bg-brand-500 px-4 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-brand-600 self-start sm:self-auto cursor-pointer"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Slide Hero Baru
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

        <!-- Grid Cards -->
        <div v-if="heroes.length > 0" class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <div
                v-for="hero in heroes"
                :key="hero.id"
                class="group flex flex-col justify-between overflow-hidden rounded-3xl bg-white border border-gray-100 shadow-sm transition duration-200 hover:-translate-y-1 hover:shadow-md"
            >
                <div>
                    <!-- Preview Image -->
                    <div class="relative aspect-video w-full overflow-hidden bg-slate-900">
                        <img
                            :src="hero.image_url"
                            :alt="hero.title"
                            class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                            @error="$event.target.src = 'https://images.unsplash.com/photo-1546445317-29f4545e9d53?w=800'"
                        />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-black/20" />

                        <!-- Order Badge -->
                        <div class="absolute top-3 left-3 flex items-center gap-1.5 rounded-full bg-slate-900/80 px-2.5 py-1 text-[10px] font-bold text-white backdrop-blur-md">
                            <span>Slide #{{ hero.order }}</span>
                        </div>

                        <!-- Status Badge -->
                        <div class="absolute top-3 right-3">
                            <span
                                class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-[10px] font-bold backdrop-blur-md"
                                :class="hero.is_active ? 'bg-emerald-500/90 text-white' : 'bg-rose-500/90 text-white'"
                            >
                                <span class="h-1.5 w-1.5 rounded-full bg-white animate-pulse" />
                                {{ hero.is_active ? 'Aktif' : 'Non-aktif' }}
                            </span>
                        </div>

                        <!-- Icon & Title Preview Overlay -->
                        <div class="absolute bottom-3 left-3 right-3 text-white">
                            <div class="flex items-center gap-1.5 text-[11px] font-semibold text-amber-300">
                                <span>🎨 Icon: {{ hero.icon_name }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-5">
                        <h3 class="text-base font-bold text-gray-900 leading-snug whitespace-pre-line">
                            {{ hero.title }}
                        </h3>
                        <p class="mt-2 text-xs leading-relaxed text-gray-600 line-clamp-3">
                            {{ hero.description }}
                        </p>
                    </div>
                </div>

                <!-- Footer Card Action -->
                <div class="border-t border-gray-100 bg-gray-50/70 p-3 flex items-center justify-between gap-2">
                    <button
                        type="button"
                        @click="toggleStatus(hero)"
                        class="rounded-xl px-2.5 py-1.5 text-[11px] font-bold transition cursor-pointer"
                        :class="hero.is_active ? 'bg-amber-50 text-amber-700 hover:bg-amber-100' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100'"
                    >
                        {{ hero.is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                    </button>

                    <div class="flex items-center gap-1.5">
                        <Link
                            :href="route('admin.heroes.edit', hero.id)"
                            class="inline-flex items-center gap-1 rounded-xl bg-white border border-gray-200 px-3 py-1.5 text-xs font-bold text-gray-700 shadow-2xs transition hover:border-brand-500 hover:text-brand-600"
                        >
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit
                        </Link>
                        <button
                            type="button"
                            @click="confirmDelete(hero)"
                            class="inline-flex items-center gap-1 rounded-xl bg-rose-50 border border-rose-200 px-3 py-1.5 text-xs font-bold text-rose-600 shadow-2xs transition hover:bg-rose-100 cursor-pointer"
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
        <div
            v-else
            class="flex flex-col items-center justify-center rounded-3xl border-2 border-dashed border-gray-200 bg-white p-12 text-center"
        >
            <div class="h-16 w-16 rounded-full bg-brand-50 flex items-center justify-center text-brand-500 mb-4">
                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <h3 class="text-base font-bold text-gray-900">Belum Ada Slide Landing Hero</h3>
            <p class="mt-1 text-xs text-gray-500 max-w-sm">Tambahkan slide baru untuk banner beranda dan pengantar onboarding aplikasi mobile.</p>
            <Link
                :href="route('admin.heroes.create')"
                class="mt-5 inline-flex items-center gap-1.5 rounded-xl bg-brand-500 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-brand-600"
            >
                Tambah Slide Sekarang
            </Link>
        </div>

        <!-- Modal Konfirmasi Hapus -->
        <transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="deleteTarget"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
            >
                <div class="w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl border border-gray-100">
                    <div class="flex items-center gap-3 text-rose-600 mb-4">
                        <div class="h-10 w-10 rounded-2xl bg-rose-50 flex items-center justify-center">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900">Konfirmasi Hapus Slide</h3>
                            <p class="text-xs text-gray-500">Tindakan ini tidak dapat dibatalkan.</p>
                        </div>
                    </div>

                    <p class="text-xs text-gray-600 mb-6 bg-gray-50 p-3 rounded-xl border border-gray-100">
                        Anda yakin ingin menghapus slide <strong>"{{ deleteTarget?.title }}"</strong>?
                    </p>

                    <div class="flex items-center justify-end gap-2">
                        <button
                            type="button"
                            @click="deleteTarget = null"
                            class="rounded-xl border border-gray-200 px-4 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 cursor-pointer"
                        >
                            Batal
                        </button>
                        <button
                            type="button"
                            @click="executeDelete"
                            class="rounded-xl bg-rose-600 px-4 py-2 text-xs font-bold text-white hover:bg-rose-700 shadow-sm cursor-pointer"
                        >
                            Ya, Hapus Slide
                        </button>
                    </div>
                </div>
            </div>
        </transition>
    </AdminLayout>
</template>
