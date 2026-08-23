<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    users: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
    stats: {
        type: Object,
        default: () => ({}),
    },
});

const page = usePage();

const searchInput = ref(props.filters.search ?? '');
const roleFilter = ref(props.filters.role ?? '');
const statusFilter = ref(props.filters.status ?? 'all');

const applyFilters = () => {
    router.get(
        route('admin.users.index'),
        {
            search: searchInput.value || undefined,
            role: roleFilter.value || undefined,
            status: statusFilter.value !== 'all' ? statusFilter.value : undefined,
        },
        { preserveState: true }
    );
};

const resetFilters = () => {
    searchInput.value = '';
    roleFilter.value = '';
    statusFilter.value = 'all';
    applyFilters();
};

const tanggal = (iso) =>
    iso
        ? new Date(iso).toLocaleDateString('id-ID', {
              day: 'numeric',
              month: 'short',
              year: 'numeric',
          })
        : '-';

// Toggle Status Aktif / Nonaktif
const toggleStatus = (user) => {
    const action = user.is_active ? 'menonaktifkan' : 'mengaktifkan';
    if (confirm(`Apakah Anda yakin ingin ${action} akun ${user.name}?`)) {
        router.patch(route('admin.users.toggle-status', user.id), {}, { preserveScroll: true });
    }
};

// Reset Password Modal
const resetTarget = ref(null);
const resetForm = useForm({
    new_password: '',
});
const submitResetPassword = () => {
    if (!resetTarget.value) return;
    resetForm.post(route('admin.users.reset-password', resetTarget.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            resetTarget.value = null;
            resetForm.reset();
        },
    });
};

// Soft Delete (Tong Sampah)
const deleteTarget = ref(null);
const deleteForm = useForm({});
const confirmSoftDelete = (user) => {
    deleteTarget.value = user;
};
const submitSoftDelete = () => {
    if (!deleteTarget.value) return;
    deleteForm.delete(route('admin.users.destroy', deleteTarget.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            deleteTarget.value = null;
        },
    });
};

// Restore
const restoreUser = (user) => {
    if (confirm(`Pulihkan akun ${user.name}?`)) {
        router.post(route('admin.users.restore', user.id), {}, { preserveScroll: true });
    }
};

// Force Delete
const forceDeleteTarget = ref(null);
const forceDeleteForm = useForm({});
const confirmForceDelete = (user) => {
    forceDeleteTarget.value = user;
};
const submitForceDelete = () => {
    if (!forceDeleteTarget.value) return;
    forceDeleteForm.delete(route('admin.users.force-delete', forceDeleteTarget.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            forceDeleteTarget.value = null;
        },
    });
};
</script>

<template>
    <Head title="Manajemen Pengguna - Admin" />

    <AdminLayout>
        <template #header>
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-bold leading-tight text-gray-900">Manajemen Pengguna</h2>
                    <p class="text-xs text-gray-500">Kelola akun pelanggan, hak akses admin, status aktifasi, reset password, dan soft delete.</p>
                </div>
                <Link
                    :href="route('admin.users.create')"
                    class="inline-flex items-center gap-1.5 rounded-xl bg-brand-500 px-4 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-brand-600 self-start sm:self-auto"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Pengguna Baru
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

        <div
            v-if="page.props.flash?.error"
            class="mb-6 flex items-center gap-3 rounded-2xl bg-rose-50 border border-rose-200 p-4 text-sm text-rose-800 shadow-sm"
        >
            <svg class="h-5 w-5 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            <span>{{ page.props.flash.error }}</span>
        </div>

        <!-- Metric Cards -->
        <div class="mb-6 grid grid-cols-2 gap-4 sm:grid-cols-4">
            <div class="rounded-2xl border border-gray-200/80 bg-white p-4 shadow-sm">
                <span class="text-xs text-gray-500 font-semibold">Total Pengguna</span>
                <p class="mt-1 text-2xl font-black text-gray-900">{{ stats.total }}</p>
            </div>
            <div class="rounded-2xl border border-gray-200/80 bg-white p-4 shadow-sm">
                <span class="text-xs text-emerald-600 font-semibold">Akun Aktif</span>
                <p class="mt-1 text-2xl font-black text-emerald-600">{{ stats.active }}</p>
            </div>
            <div class="rounded-2xl border border-gray-200/80 bg-white p-4 shadow-sm">
                <span class="text-xs text-amber-600 font-semibold">Nonaktif</span>
                <p class="mt-1 text-2xl font-black text-amber-600">{{ stats.inactive }}</p>
            </div>
            <div class="rounded-2xl border border-gray-200/80 bg-white p-4 shadow-sm">
                <span class="text-xs text-rose-600 font-semibold">Tong Sampah (Trashed)</span>
                <p class="mt-1 text-2xl font-black text-rose-600">{{ stats.trashed }}</p>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="mb-6 rounded-2xl border border-gray-200/80 bg-white p-4 shadow-sm">
            <div class="flex flex-wrap items-center gap-3">
                <!-- Search Input -->
                <div class="relative flex-1 min-w-[220px]">
                    <input
                        v-model="searchInput"
                        type="text"
                        placeholder="Cari nama atau email..."
                        class="w-full rounded-xl border-gray-300 bg-gray-50/50 py-2 pl-9 pr-4 text-xs focus:border-brand-500 focus:bg-white focus:ring-brand-500"
                        @keyup.enter="applyFilters"
                    />
                    <svg class="absolute left-3 top-2.5 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                <!-- Role Filter -->
                <div class="w-40">
                    <select
                        v-model="roleFilter"
                        class="w-full rounded-xl border-gray-300 bg-gray-50/50 py-2 text-xs focus:border-brand-500 focus:bg-white focus:ring-brand-500"
                        @change="applyFilters"
                    >
                        <option value="">Semua Peran</option>
                        <option value="user">Pelanggan (User)</option>
                        <option value="admin">Administrator</option>
                    </select>
                </div>

                <!-- Status Filter -->
                <div class="w-44">
                    <select
                        v-model="statusFilter"
                        class="w-full rounded-xl border-gray-300 bg-gray-50/50 py-2 text-xs focus:border-brand-500 focus:bg-white focus:ring-brand-500"
                        @change="applyFilters"
                    >
                        <option value="all">Semua Status</option>
                        <option value="active">Hanya Aktif</option>
                        <option value="inactive">Hanya Nonaktif</option>
                        <option value="trashed">Tong Sampah (Trashed)</option>
                    </select>
                </div>

                <button
                    type="button"
                    @click="applyFilters"
                    class="rounded-xl bg-gray-900 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-brand-600 transition"
                >
                    Terapkan
                </button>

                <button
                    type="button"
                    @click="resetFilters"
                    class="rounded-xl border border-gray-300 bg-white px-3.5 py-2 text-xs font-semibold text-gray-700 shadow-sm hover:bg-gray-50 transition"
                >
                    Reset
                </button>
            </div>
        </div>

        <!-- Users Table -->
        <div class="overflow-hidden rounded-2xl border border-gray-200/80 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="border-b border-gray-200 bg-gray-50/75 text-[11px] font-bold uppercase tracking-wider text-gray-500">
                        <tr>
                            <th class="px-5 py-3.5">Nama & Email</th>
                            <th class="px-5 py-3.5">Peran (Role)</th>
                            <th class="px-5 py-3.5">Total Transaksi</th>
                            <th class="px-5 py-3.5">Status Akun</th>
                            <th class="px-5 py-3.5">Terdaftar</th>
                            <th class="px-5 py-3.5 text-right">Aksi Manajemen</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr
                            v-for="u in users.data"
                            :key="u.id"
                            class="hover:bg-gray-50/75 transition duration-150"
                            :class="u.is_trashed ? 'bg-rose-50/30' : ''"
                        >
                            <!-- User Info -->
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand-50 text-brand-700 font-bold border border-brand-100">
                                        {{ u.name.charAt(0).toUpperCase() }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900 flex items-center gap-1.5">
                                            {{ u.name }}
                                            <span v-if="u.id === $page.props.auth.user.id" class="rounded bg-brand-100 px-1.5 py-0.5 text-[9px] font-bold text-brand-800">
                                                Anda
                                            </span>
                                        </p>
                                        <p class="text-[11px] text-gray-400">{{ u.email }}</p>
                                    </div>
                                </div>
                            </td>

                            <!-- Role -->
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span
                                    class="inline-flex items-center rounded-lg px-2.5 py-1 text-[11px] font-bold"
                                    :class="u.role === 'admin' ? 'bg-purple-50 text-purple-700 border border-purple-200' : 'bg-gray-100 text-gray-700'"
                                >
                                    {{ u.role === 'admin' ? '👑 Administrator' : '👤 Pelanggan' }}
                                </span>
                            </td>

                            <!-- Transaksi Count -->
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="font-semibold text-gray-700">
                                    {{ u.transactions_count }} Pesanan
                                </span>
                            </td>

                            <!-- Status Aktif / Trashed -->
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span
                                    v-if="u.is_trashed"
                                    class="inline-flex items-center rounded-full bg-rose-100 px-2.5 py-0.5 text-[10px] font-bold text-rose-700 border border-rose-200"
                                >
                                    🗑️ Terhapus (Trashed)
                                </span>
                                <span
                                    v-else-if="u.is_active"
                                    class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-[10px] font-bold text-emerald-700 border border-emerald-200"
                                >
                                    <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                    Aktif
                                </span>
                                <span
                                    v-else
                                    class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-0.5 text-[10px] font-bold text-amber-700 border border-amber-200"
                                >
                                    <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                    Dinonaktifkan
                                </span>
                            </td>

                            <!-- Tanggal Daftar -->
                            <td class="px-5 py-4 text-gray-400 whitespace-nowrap">
                                {{ tanggal(u.created_at) }}
                            </td>

                            <!-- Aksi -->
                            <td class="px-5 py-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- Jika Masih Aktif/Normal -->
                                    <template v-if="!u.is_trashed">
                                        <!-- Toggle Status Aktif/Nonaktif -->
                                        <button
                                            v-if="u.id !== $page.props.auth.user.id"
                                            type="button"
                                            class="inline-flex items-center rounded-lg px-2.5 py-1.5 text-xs font-semibold shadow-xs transition"
                                            :class="
                                                u.is_active
                                                    ? 'bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200'
                                                    : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200'
                                            "
                                            :title="u.is_active ? 'Nonaktifkan Akun' : 'Aktifkan Akun'"
                                            @click="toggleStatus(u)"
                                        >
                                            {{ u.is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>

                                        <!-- Reset Password -->
                                        <button
                                            type="button"
                                            class="inline-flex items-center rounded-lg bg-gray-100 px-2.5 py-1.5 text-xs font-semibold text-gray-700 transition hover:bg-brand-500 hover:text-white"
                                            title="Reset Password Pengguna"
                                            @click="resetTarget = u"
                                        >
                                            Reset Sandi
                                        </button>

                                        <!-- Edit -->
                                        <Link
                                            :href="route('admin.users.edit', u.id)"
                                            class="inline-flex items-center rounded-lg bg-gray-100 px-2.5 py-1.5 text-xs font-semibold text-gray-700 transition hover:bg-gray-200"
                                            title="Edit Profil Akun"
                                        >
                                            Edit
                                        </Link>

                                        <!-- Soft Delete -->
                                        <button
                                            v-if="u.id !== $page.props.auth.user.id"
                                            type="button"
                                            class="inline-flex items-center rounded-lg bg-rose-50 px-2.5 py-1.5 text-xs font-semibold text-rose-600 transition hover:bg-rose-600 hover:text-white"
                                            title="Pindahkan ke Tong Sampah (Soft Delete)"
                                            @click="confirmSoftDelete(u)"
                                        >
                                            Hapus
                                        </button>
                                    </template>

                                    <!-- Jika Sedang di Tong Sampah (Trashed) -->
                                    <template v-else>
                                        <button
                                            type="button"
                                            class="inline-flex items-center rounded-lg bg-emerald-600 px-2.5 py-1.5 text-xs font-bold text-white shadow-sm transition hover:bg-emerald-700"
                                            @click="restoreUser(u)"
                                        >
                                            Pulihkan
                                        </button>
                                        <button
                                            v-if="u.id !== $page.props.auth.user.id"
                                            type="button"
                                            class="inline-flex items-center rounded-lg bg-rose-600 px-2.5 py-1.5 text-xs font-bold text-white shadow-sm transition hover:bg-rose-700"
                                            @click="confirmForceDelete(u)"
                                        >
                                            Hapus Permanen
                                        </button>
                                    </template>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!users.data.length">
                            <td colspan="6" class="px-5 py-12 text-center text-gray-400">
                                <svg class="mx-auto h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <p class="mt-2 text-sm font-semibold text-gray-700">Tidak ada pengguna ditemukan.</p>
                                <p class="text-xs text-gray-400">Coba ubah kata kunci pencarian atau filter status.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="users.links?.length > 3" class="border-t border-gray-100 bg-gray-50/50 px-5 py-3.5 flex flex-wrap justify-between items-center gap-2">
                <span class="text-xs text-gray-500">
                    Menampilkan total {{ users.total }} pengguna
                </span>
                <div class="flex flex-wrap gap-1">
                    <template v-for="(link, i) in users.links" :key="i">
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            class="rounded-lg px-3 py-1.5 text-xs font-semibold transition"
                            :class="link.active ? 'bg-brand-500 text-white shadow-sm' : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200'"
                            v-html="link.label"
                        />
                        <span v-else class="px-3 py-1.5 text-xs text-gray-400" v-html="link.label" />
                    </template>
                </div>
            </div>
        </div>

        <!-- Modal Reset Password -->
        <div
            v-if="resetTarget"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm"
            @click.self="resetTarget = null"
        >
            <form class="w-full max-w-md space-y-4 rounded-2xl bg-white p-6 shadow-2xl" @submit.prevent="submitResetPassword">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-50 text-brand-700">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900">
                            Reset Password Pengguna
                        </h3>
                        <p class="text-xs text-gray-500 font-semibold">{{ resetTarget.name }} ({{ resetTarget.email }})</p>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700">Password Baru *</label>
                    <input
                        v-model="resetForm.new_password"
                        type="text"
                        placeholder="Masukkan kata sandi baru (min. 8 karakter)..."
                        class="mt-1 block w-full rounded-xl border-gray-300 text-xs shadow-sm focus:border-brand-500 focus:ring-brand-500"
                        required
                    />
                    <p v-if="resetForm.errors.new_password" class="mt-1 text-xs text-rose-600">{{ resetForm.errors.new_password }}</p>
                </div>

                <div class="flex justify-end gap-2 border-t border-gray-100 pt-3">
                    <button
                        type="button"
                        class="rounded-xl border border-gray-300 bg-white px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50"
                        @click="resetTarget = null"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        class="rounded-xl bg-brand-500 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-brand-600"
                        :disabled="resetForm.processing"
                    >
                        {{ resetForm.processing ? 'Menyimpan...' : 'Simpan Password Baru' }}
                    </button>
                </div>
            </form>
        </div>

        <!-- Modal Konfirmasi Soft Delete -->
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
                            Pindahkan ke Tong Sampah?
                        </h3>
                        <p class="text-xs text-gray-500 font-semibold">{{ deleteTarget.name }}</p>
                    </div>
                </div>

                <p class="text-xs text-gray-600 leading-relaxed">
                    Pengguna ini akan dipindahkan ke tong sampah (Soft Delete). Akun tidak akan bisa login, namun riwayat transaksi tetap tersimpan dan akun dapat dipulihkan sewaktu-waktu.
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
                        :disabled="deleteForm.processing"
                        @click="submitSoftDelete"
                    >
                        {{ deleteForm.processing ? 'Menghapus...' : 'Ya, Nonaktifkan Akun' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Konfirmasi Force Delete (Permanen) -->
        <div
            v-if="forceDeleteTarget"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm"
            @click.self="forceDeleteTarget = null"
        >
            <div class="w-full max-w-md space-y-4 rounded-2xl bg-white p-6 shadow-2xl">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-rose-100 text-rose-700">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900">
                            Hapus Akun Secara Permanen?
                        </h3>
                        <p class="text-xs text-gray-500 font-semibold">{{ forceDeleteTarget.name }}</p>
                    </div>
                </div>

                <p class="text-xs text-rose-600 leading-relaxed font-semibold">
                    PERINGATAN: Tindakan ini akan menghapus akun secara permanen dari database dan tidak dapat dipulihkan kembali!
                </p>

                <div class="flex justify-end gap-2 border-t border-gray-100 pt-3">
                    <button
                        type="button"
                        class="rounded-xl border border-gray-300 bg-white px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50"
                        @click="forceDeleteTarget = null"
                    >
                        Batal
                    </button>
                    <button
                        type="button"
                        class="rounded-xl bg-rose-600 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-rose-700"
                        :disabled="forceDeleteForm.processing"
                        @click="submitForceDelete"
                    >
                        {{ forceDeleteForm.processing ? 'Menghapus...' : 'Hapus Permanen' }}
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
