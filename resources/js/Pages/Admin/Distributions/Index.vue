<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    distributions: {
        type: Array,
        required: true,
    },
});

const deleteTarget = ref(null);

const confirmDelete = (dist) => {
    deleteTarget.value = dist;
};

const executeDelete = () => {
    if (!deleteTarget.value) return;
    router.delete(route('admin.distribusi.destroy', deleteTarget.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            deleteTarget.value = null;
        },
    });
};

const toggleStatus = (dist) => {
    router.patch(route('admin.distribusi.toggle-status', dist.id), {}, {
        preserveScroll: true,
    });
};

const formatRupiah = (v) =>
    new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(v);
</script>

<template>
    <Head title="Opsi Penyaluran & Distribusi - Admin" />

    <AdminLayout>
        <!-- MOBILE TOP HEADER -->
        <div class="block md:hidden bg-gradient-to-b from-slate-900 via-zinc-900 to-zinc-900 text-white pt-4 pb-6 px-4 -mx-3 -mt-4 rounded-b-[2rem] shadow-xl relative overflow-hidden mb-5">
            <div class="flex items-center justify-between relative z-10">
                <div>
                    <span class="text-[10px] text-zinc-400 font-bold uppercase tracking-wider block">Master Data</span>
                    <h2 class="text-base font-black text-white leading-tight">Penyaluran / Distribusi</h2>
                </div>
                <Link
                    :href="route('admin.distribusi.create')"
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
                    <h2 class="text-xl font-bold leading-tight text-gray-900">Opsi Penyaluran & Distribusi</h2>
                    <p class="text-xs text-gray-500">Kelola tujuan penyaluran daging qurban/aqiqah (Indonesia, Makkah, dll) beserta multi-bahasa dan biayanya.</p>
                </div>
                <Link
                    :href="route('admin.distribusi.create')"
                    class="inline-flex items-center gap-1.5 rounded-xl bg-brand-500 px-4 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-brand-600 self-start sm:self-auto"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Opsi Penyaluran
                </Link>
            </div>
        </template>

        <div v-if="distributions.length" class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
            <div
                v-for="dist in distributions"
                :key="dist.id"
                class="flex flex-col justify-between overflow-hidden rounded-2xl border border-gray-200/80 bg-white p-5 shadow-sm transition hover:border-brand-500/40 hover:shadow-md"
            >
                <div>
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-brand-600 bg-brand-50 px-2 py-0.5 rounded-md">
                                Urutan #{{ dist.order }}
                            </span>
                            <h3 class="mt-2 text-base font-bold text-gray-900">{{ dist.name_id }}</h3>
                            <div class="flex flex-wrap gap-1 mt-1 text-[11px] text-gray-500">
                                <span v-if="dist.name_en" class="bg-gray-100 px-1.5 py-0.5 rounded">EN: {{ dist.name_en }}</span>
                                <span v-if="dist.name_zh" class="bg-gray-100 px-1.5 py-0.5 rounded">ZH: {{ dist.name_zh }}</span>
                                <span v-if="dist.name_ar" class="bg-gray-100 px-1.5 py-0.5 rounded">AR: {{ dist.name_ar }}</span>
                            </div>
                        </div>
                        <button
                            @click="toggleStatus(dist)"
                            type="button"
                            class="inline-flex items-center rounded-full px-2.5 py-1 text-[10px] font-bold cursor-pointer"
                            :class="dist.is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-gray-100 text-gray-600 border border-gray-200'"
                        >
                            {{ dist.is_active ? '● Aktif' : '○ Nonaktif' }}
                        </button>
                    </div>

                    <p class="mt-3 text-xs text-gray-600 line-clamp-2">
                        {{ dist.description_id || 'Tidak ada deskripsi.' }}
                    </p>

                    <div class="mt-4 border-t border-gray-100 pt-3">
                        <span class="text-[11px] text-gray-400 font-semibold block">Biaya Penyaluran / Ongkir Tambahan:</span>
                        <div class="mt-1 flex flex-wrap gap-2 text-xs font-bold text-gray-800">
                            <span>IDR: {{ formatRupiah(dist.fee_idr) }}</span>
                            <span v-if="dist.fee_usd > 0" class="text-gray-500">USD: ${{ Number(dist.fee_usd) }}</span>
                            <span v-if="dist.fee_cny > 0" class="text-gray-500">CNY: ¥{{ Number(dist.fee_cny) }}</span>
                            <span v-if="dist.fee_sar > 0" class="text-gray-500">SAR: ﷼{{ Number(dist.fee_sar) }}</span>
                        </div>
                    </div>
                </div>

                <div class="mt-5 flex items-center justify-end gap-2 border-t border-gray-100 pt-3">
                    <Link
                        :href="route('admin.distribusi.edit', dist.id)"
                        class="rounded-xl border border-gray-200 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 hover:bg-gray-50"
                    >
                        Ubah
                    </Link>
                    <button
                        type="button"
                        @click="confirmDelete(dist)"
                        class="rounded-xl border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-bold text-rose-600 hover:bg-rose-100"
                    >
                        Hapus
                    </button>
                </div>
            </div>
        </div>

        <div v-else class="rounded-2xl border border-dashed border-gray-300 p-12 text-center bg-white">
            <p class="text-sm font-semibold text-gray-600">Belum ada data opsi penyaluran / distribusi.</p>
            <Link
                :href="route('admin.distribusi.create')"
                class="mt-3 inline-flex items-center gap-1 rounded-xl bg-brand-500 px-4 py-2 text-xs font-bold text-white shadow-xs hover:bg-brand-600"
            >
                Tambah Opsi Pertama
            </Link>
        </div>

        <!-- Modal Konfirmasi Hapus -->
        <div
            v-if="deleteTarget"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-xs"
        >
            <div class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-xl">
                <h3 class="text-base font-bold text-gray-900">Hapus Opsi Penyaluran</h3>
                <p class="mt-2 text-xs text-gray-600">
                    Apakah Anda yakin ingin menghapus opsi penyaluran <strong>{{ deleteTarget.name_id }}</strong>?
                </p>
                <div class="mt-5 flex justify-end gap-2">
                    <button
                        type="button"
                        @click="deleteTarget = null"
                        class="rounded-xl border border-gray-200 px-4 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50"
                    >
                        Batal
                    </button>
                    <button
                        type="button"
                        @click="executeDelete"
                        class="rounded-xl bg-rose-600 px-4 py-2 text-xs font-bold text-white hover:bg-rose-700"
                    >
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
