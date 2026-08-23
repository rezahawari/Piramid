<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    user: {
        type: Object,
        default: null,
    },
});

const isEdit = computed(() => props.user !== null);

const form = useForm({
    name: props.user?.name ?? '',
    email: props.user?.email ?? '',
    password: '',
    role: props.user?.role ?? 'user',
    is_active: props.user?.is_active ?? true,
});

const submit = () => {
    if (isEdit.value) {
        form.put(route('admin.users.update', props.user.id));
    } else {
        form.post(route('admin.users.store'));
    }
};
</script>

<template>
    <Head :title="isEdit ? `Ubah Pengguna: ${user.name}` : 'Tambah Pengguna Baru'" />

    <AdminLayout>
        <template #header>
            <div class="flex items-center gap-2">
                <Link :href="route('admin.users.index')" class="text-xs font-semibold text-gray-500 hover:text-brand-600">
                    &larr; Kembali ke Daftar Pengguna
                </Link>
                <span class="text-xs text-gray-300">/</span>
                <span class="text-xs font-bold text-gray-800">{{ isEdit ? 'Ubah Pengguna' : 'Pengguna Baru' }}</span>
            </div>
            <h2 class="mt-1 text-xl font-bold leading-tight text-gray-900">
                {{ isEdit ? `Ubah Profil Pengguna: ${user.name}` : 'Tambah Pengguna Baru' }}
            </h2>
        </template>

        <div class="mx-auto max-w-2xl">
            <form
                class="space-y-6 rounded-2xl border border-gray-200/80 bg-white p-6 sm:p-8 shadow-sm"
                @submit.prevent="submit"
            >
                <div class="border-b border-gray-100 pb-5">
                    <h3 class="text-base font-bold text-gray-900">Profil Akun Pengguna</h3>
                    <p class="text-xs text-gray-500">Lengkapi nama lengkap, alamat email aktif, dan peran akses pengguna.</p>

                    <div class="mt-5 space-y-4">
                        <!-- Nama -->
                        <div>
                            <InputLabel for="name" value="Nama Lengkap *" class="!text-xs font-bold" />
                            <TextInput
                                id="name"
                                v-model="form.name"
                                type="text"
                                class="mt-1 block w-full !rounded-xl !text-xs"
                                placeholder="Nama Pengguna"
                                required
                                autofocus
                            />
                            <InputError class="mt-1" :message="form.errors.name" />
                        </div>

                        <!-- Email -->
                        <div>
                            <InputLabel for="email" value="Alamat Email *" class="!text-xs font-bold" />
                            <TextInput
                                id="email"
                                v-model="form.email"
                                type="email"
                                class="mt-1 block w-full !rounded-xl !text-xs"
                                placeholder="user@example.com"
                                required
                            />
                            <InputError class="mt-1" :message="form.errors.email" />
                        </div>

                        <!-- Password (hanya saat create) -->
                        <div v-if="!isEdit">
                            <InputLabel for="password" value="Kata Sandi Awal *" class="!text-xs font-bold" />
                            <TextInput
                                id="password"
                                v-model="form.password"
                                type="password"
                                class="mt-1 block w-full !rounded-xl !text-xs"
                                placeholder="Minimal 8 karakter..."
                                required
                            />
                            <InputError class="mt-1" :message="form.errors.password" />
                        </div>

                        <!-- Peran / Role -->
                        <div>
                            <InputLabel for="role" value="Peran Akses (Role) *" class="!text-xs font-bold" />
                            <select
                                id="role"
                                v-model="form.role"
                                class="mt-1 block w-full rounded-xl border-gray-300 text-xs shadow-sm focus:border-brand-500 focus:ring-brand-500"
                                required
                            >
                                <option value="user">👤 Pelanggan Biasa (User)</option>
                                <option value="admin">👑 Administrator (Akses Penuh CMS)</option>
                            </select>
                            <InputError class="mt-1" :message="form.errors.role" />
                        </div>
                    </div>
                </div>

                <!-- Status Aktifasi -->
                <div class="pt-1">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <Checkbox v-model:checked="form.is_active" class="!rounded-md" />
                        <div>
                            <span class="text-xs font-bold text-gray-900 block">
                                Status Akun Aktif
                            </span>
                            <span class="text-[11px] text-gray-500 block">
                                Pengguna dapat login dan melakukan transaksi qurban/aqiqah jika status aktif.
                            </span>
                        </div>
                    </label>
                    <InputError class="mt-1" :message="form.errors.is_active" />
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-5">
                    <Link :href="route('admin.users.index')">
                        <SecondaryButton type="button" class="!rounded-xl !text-xs !py-2.5">
                            Batal
                        </SecondaryButton>
                    </Link>
                    <PrimaryButton
                        class="!rounded-xl !text-xs !py-2.5 !bg-brand-500 hover:!bg-brand-600 shadow-sm"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Menyimpan...' : (isEdit ? 'Simpan Perubahan' : 'Buat Pengguna') }}
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
