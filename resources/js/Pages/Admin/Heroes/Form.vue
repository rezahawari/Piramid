<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    hero: {
        type: Object,
        default: null,
    },
});

const isEdit = Boolean(props.hero);

const form = useForm({
    title: props.hero?.title ?? '',
    description: props.hero?.description ?? '',
    image_file: null,
    image_url: props.hero?.image_url ?? '',
    icon_name: props.hero?.icon_name ?? 'verified_user_rounded',
    order: props.hero?.order ?? 1,
    is_active: props.hero?.is_active ?? true,
});

const previewUrl = ref(props.hero?.image_url ?? '');

const iconOptions = [
    { value: 'verified_user_rounded', label: 'Verified Shield (Kepercayaan / Syariat)', preview: '🛡️' },
    { value: 'videocam_rounded', label: 'Videocam (Laporan Video & Foto Realtime)', preview: '📹' },
    { value: 'volunteer_activism_rounded', label: 'Heart in Hand (Penyaluran Dhuafa / Sedekah)', preview: '🤝' },
    { value: 'local_shipping_rounded', label: 'Shipping Truck (Distribusi Cepat)', preview: '🚚' },
    { value: 'workspace_premium_rounded', label: 'Premium Award (Hewan Berkualitas)', preview: '🏆' },
];

const handleFileChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.image_file = file;
        previewUrl.value = URL.createObjectURL(file);
    }
};

const handleUrlInput = () => {
    if (!form.image_file) {
        previewUrl.value = form.image_url;
    }
};

const submit = () => {
    if (isEdit) {
        form.transform((data) => ({
            ...data,
            _method: 'PUT',
        })).post(route('admin.heroes.update', props.hero.id), {
            forceFormData: true,
        });
    } else {
        form.post(route('admin.heroes.store'), {
            forceFormData: true,
        });
    }
};
</script>

<template>
    <Head :title="isEdit ? 'Edit Slide Hero - Admin' : 'Tambah Slide Hero - Admin'" />

    <AdminLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <Link
                    :href="route('admin.heroes.index')"
                    class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 hover:bg-gray-200 transition cursor-pointer"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                    </svg>
                </Link>
                <div>
                    <h2 class="text-xl font-bold leading-tight text-gray-900">
                        {{ isEdit ? 'Edit Slide Hero & Onboarding' : 'Tambah Slide Hero & Onboarding Baru' }}
                    </h2>
                    <p class="text-xs text-gray-500">Konfigurasi visual dan teks yang akan tampil di aplikasi mobile dan web publik.</p>
                </div>
            </div>
        </template>

        <div class="mx-auto max-w-3xl">
            <form @submit.prevent="submit" class="space-y-6">
                <!-- Card Form -->
                <div class="rounded-3xl bg-white p-6 shadow-sm border border-gray-100 space-y-5">
                    <!-- Judul Slide -->
                    <div>
                        <label class="block text-xs font-bold text-gray-800 uppercase tracking-wider mb-1.5">
                            Judul Slide (Title) <span class="text-rose-500">*</span>
                        </label>
                        <textarea
                            v-model="form.title"
                            rows="2"
                            placeholder="Contoh: Pilih Hewan Terbaik&#10;Sesuai Syariat"
                            class="w-full rounded-2xl border border-gray-200 p-3 text-sm focus:border-brand-500 focus:ring-brand-500 font-medium"
                            required
                        />
                        <p class="mt-1 text-[11px] text-gray-400">Gunakan baris baru (Enter) jika ingin membagi judul menjadi 2 baris di tampilan mobile.</p>
                        <p v-if="form.errors.title" class="mt-1 text-xs text-rose-500 font-semibold">{{ form.errors.title }}</p>
                    </div>

                    <!-- Deskripsi Slide -->
                    <div>
                        <label class="block text-xs font-bold text-gray-800 uppercase tracking-wider mb-1.5">
                            Deskripsi / Subtitle <span class="text-rose-500">*</span>
                        </label>
                        <textarea
                            v-model="form.description"
                            rows="3"
                            placeholder="Tuliskan penjelasan singkat mengenai keunggulan Piramid..."
                            class="w-full rounded-2xl border border-gray-200 p-3 text-sm focus:border-brand-500 focus:ring-brand-500"
                            required
                        />
                        <p v-if="form.errors.description" class="mt-1 text-xs text-rose-500 font-semibold">{{ form.errors.description }}</p>
                    </div>

                    <!-- Icon Material -->
                    <div>
                        <label class="block text-xs font-bold text-gray-800 uppercase tracking-wider mb-1.5">
                            Ikon Material (Mobile App Icon)
                        </label>
                        <select
                            v-model="form.icon_name"
                            class="w-full rounded-2xl border border-gray-200 p-3 text-sm focus:border-brand-500 focus:ring-brand-500 bg-white"
                        >
                            <option v-for="opt in iconOptions" :key="opt.value" :value="opt.value">
                                {{ opt.preview }} {{ opt.label }} ({{ opt.value }})
                            </option>
                        </select>
                        <p v-if="form.errors.icon_name" class="mt-1 text-xs text-rose-500 font-semibold">{{ form.errors.icon_name }}</p>
                    </div>

                    <!-- Upload Gambar / Banner -->
                    <div>
                        <label class="block text-xs font-bold text-gray-800 uppercase tracking-wider mb-1.5">
                            Gambar Banner Slide <span class="text-xs text-gray-400 font-normal lowercase">(opsional, ada fallback gambar default)</span>
                        </label>
                        
                        <!-- Image Preview -->
                        <div v-if="previewUrl" class="mb-3 relative aspect-video w-full max-w-md overflow-hidden rounded-2xl bg-slate-900 border border-gray-200 shadow-inner">
                            <img :src="previewUrl" alt="Preview" class="h-full w-full object-cover" />
                            <div class="absolute bottom-2 left-2 rounded-lg bg-black/60 px-2 py-1 text-[10px] text-white backdrop-blur-sm">
                                Live Preview
                            </div>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-2">
                            <div>
                                <label class="block text-[11px] font-semibold text-gray-600 mb-1">Pilih Berkas Foto Lokal (Max 5MB):</label>
                                <input
                                    type="file"
                                    accept="image/*"
                                    @change="handleFileChange"
                                    class="w-full text-xs text-gray-500 file:mr-3 file:rounded-xl file:border-0 file:bg-brand-50 file:px-3 file:py-2 file:text-xs file:font-bold file:text-brand-700 hover:file:bg-brand-100"
                                />
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-gray-600 mb-1">Atau Gunakan Direct URL Gambar (Unsplash/CDN):</label>
                                <input
                                    type="url"
                                    v-model="form.image_url"
                                    @input="handleUrlInput"
                                    placeholder="https://images.unsplash.com/..."
                                    class="w-full rounded-xl border border-gray-200 p-2 text-xs focus:border-brand-500 focus:ring-brand-500"
                                />
                            </div>
                        </div>
                        <p v-if="form.errors.image_file" class="mt-1 text-xs text-rose-500 font-semibold">{{ form.errors.image_file }}</p>
                        <p v-if="form.errors.image_url" class="mt-1 text-xs text-rose-500 font-semibold">{{ form.errors.image_url }}</p>
                    </div>

                    <!-- Nomor Urut Slide & Status Aktif -->
                    <div class="grid gap-4 sm:grid-cols-2 border-t border-gray-100 pt-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-800 uppercase tracking-wider mb-1.5">
                                Urutan Tampilan (Order) <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="number"
                                v-model="form.order"
                                min="1"
                                class="w-full rounded-2xl border border-gray-200 p-3 text-sm focus:border-brand-500 focus:ring-brand-500 font-bold"
                                required
                            />
                            <p class="mt-1 text-[11px] text-gray-400">Slide dengan nomor lebih kecil akan tampil lebih awal.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-800 uppercase tracking-wider mb-1.5">
                                Status Publikasi
                            </label>
                            <div class="flex items-center gap-3 mt-3">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" v-model="form.is_active" class="sr-only peer" />
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-hidden rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500" />
                                </label>
                                <span class="text-xs font-bold" :class="form.is_active ? 'text-emerald-600' : 'text-gray-400'">
                                    {{ form.is_active ? 'Aktif (Tampil di App & Web)' : 'Draft / Non-aktif' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-3">
                    <Link
                        :href="route('admin.heroes.index')"
                        class="rounded-2xl border border-gray-200 bg-white px-5 py-2.5 text-xs font-bold text-gray-700 shadow-2xs hover:bg-gray-50 transition cursor-pointer"
                    >
                        Batal
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center gap-2 rounded-2xl bg-brand-500 px-6 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-brand-600 transition disabled:opacity-50 cursor-pointer"
                    >
                        <svg v-if="form.processing" class="h-4 w-4 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                        </svg>
                        <span>{{ isEdit ? 'Simpan Perubahan' : 'Tambah Slide Sekarang' }}</span>
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
