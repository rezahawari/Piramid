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
    distribution: {
        type: Object,
        default: null,
    },
});

const isEdit = computed(() => props.distribution !== null);

const form = useForm({
    _method: isEdit.value ? 'PUT' : 'POST',
    name_id: props.distribution?.name_id ?? '',
    name_en: props.distribution?.name_en ?? '',
    name_zh: props.distribution?.name_zh ?? '',
    name_ar: props.distribution?.name_ar ?? '',
    description_id: props.distribution?.description_id ?? '',
    description_en: props.distribution?.description_en ?? '',
    description_zh: props.distribution?.description_zh ?? '',
    description_ar: props.distribution?.description_ar ?? '',
    fee_idr: props.distribution?.fee_idr != null ? String(props.distribution.fee_idr) : '0',
    fee_usd: props.distribution?.fee_usd != null ? String(props.distribution.fee_usd) : '0',
    fee_cny: props.distribution?.fee_cny != null ? String(props.distribution.fee_cny) : '0',
    fee_sar: props.distribution?.fee_sar != null ? String(props.distribution.fee_sar) : '0',
    order: props.distribution?.order != null ? Number(props.distribution.order) : 0,
    is_active: props.distribution?.is_active ?? true,
});

const submit = () => {
    if (isEdit.value) {
        form.put(route('admin.distribusi.update', props.distribution.id));
    } else {
        form.post(route('admin.distribusi.store'));
    }
};
</script>

<template>
    <Head :title="isEdit ? `Ubah Opsi Penyaluran: ${distribution.name_id}` : 'Tambah Opsi Penyaluran Baru'" />

    <AdminLayout>
        <template #header>
            <div class="flex items-center gap-2">
                <Link :href="route('admin.distribusi.index')" class="text-xs font-semibold text-gray-500 hover:text-brand-600">
                    &larr; Kembali ke Penyaluran / Distribusi
                </Link>
                <span class="text-xs text-gray-300">/</span>
                <span class="text-xs font-bold text-gray-800">{{ isEdit ? 'Ubah Opsi' : 'Opsi Baru' }}</span>
            </div>
            <h2 class="mt-1 text-xl font-bold leading-tight text-gray-900">
                {{ isEdit ? `Ubah Opsi: ${distribution.name_id}` : 'Tambah Opsi Penyaluran Baru' }}
            </h2>
        </template>

        <div class="mx-auto max-w-3xl">
            <form
                class="space-y-6 rounded-2xl border border-gray-200/80 bg-white p-6 sm:p-8 shadow-sm"
                @submit.prevent="submit"
            >
                <!-- 1. Nama Penyaluran (Multibahasa) -->
                <div class="border-b border-gray-100 pb-5">
                    <h3 class="text-base font-bold text-gray-900">Nama Tujuan Penyaluran</h3>
                    <p class="text-xs text-gray-500">Masukkan nama wilayah atau skema penyaluran dalam berbagai bahasa.</p>

                    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <InputLabel for="name_id" value="Nama (Bahasa Indonesia) *" class="!text-xs font-bold" />
                            <TextInput
                                id="name_id"
                                v-model="form.name_id"
                                type="text"
                                class="mt-1 block w-full !rounded-xl !text-xs"
                                placeholder="Contoh: Indonesia (Pelosok & Dhuafa)"
                                required
                            />
                            <InputError class="mt-1" :message="form.errors.name_id" />
                        </div>

                        <div>
                            <InputLabel for="name_en" value="Nama (English)" class="!text-xs font-bold" />
                            <TextInput
                                id="name_en"
                                v-model="form.name_en"
                                type="text"
                                class="mt-1 block w-full !rounded-xl !text-xs"
                                placeholder="e.g. Indonesia (Remote & Needy)"
                            />
                            <InputError class="mt-1" :message="form.errors.name_en" />
                        </div>

                        <div>
                            <InputLabel for="name_zh" value="Nama (中文 Chinese)" class="!text-xs font-bold" />
                            <TextInput
                                id="name_zh"
                                v-model="form.name_zh"
                                type="text"
                                class="mt-1 block w-full !rounded-xl !text-xs"
                                placeholder="例如: 印度尼西亚（偏远与贫困地区）"
                            />
                            <InputError class="mt-1" :message="form.errors.name_zh" />
                        </div>

                        <div>
                            <InputLabel for="name_ar" value="Nama (العربية Arabic)" class="!text-xs font-bold" />
                            <TextInput
                                id="name_ar"
                                v-model="form.name_ar"
                                type="text"
                                dir="rtl"
                                class="mt-1 block w-full !rounded-xl !text-xs"
                                placeholder="مثال: إندونيسيا (المناطق النائية والمحتاجين)"
                            />
                            <InputError class="mt-1" :message="form.errors.name_ar" />
                        </div>
                    </div>
                </div>

                <!-- 2. Deskripsi Penyaluran -->
                <div class="border-b border-gray-100 pb-5">
                    <h3 class="text-base font-bold text-gray-900">Deskripsi Penyaluran</h3>
                    <div class="mt-4 space-y-4">
                        <div>
                            <InputLabel for="description_id" value="Deskripsi (Indonesia)" class="!text-xs font-bold" />
                            <textarea
                                id="description_id"
                                v-model="form.description_id"
                                rows="2"
                                class="mt-1 block w-full rounded-xl border-gray-300 text-xs shadow-xs focus:border-brand-500 focus:ring-brand-500"
                                placeholder="Daging akan disembelih dan didistribusikan ke warga dhuafa di pelosok desa binaan."
                            ></textarea>
                            <InputError class="mt-1" :message="form.errors.description_id" />
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                            <div>
                                <InputLabel for="description_en" value="Deskripsi (English)" class="!text-xs font-bold" />
                                <textarea
                                    id="description_en"
                                    v-model="form.description_en"
                                    rows="2"
                                    class="mt-1 block w-full rounded-xl border-gray-300 text-xs shadow-xs focus:border-brand-500 focus:ring-brand-500"
                                    placeholder="Meat will be distributed to needy communities in rural areas."
                                ></textarea>
                            </div>
                            <div>
                                <InputLabel for="description_zh" value="Deskripsi (Chinese)" class="!text-xs font-bold" />
                                <textarea
                                    id="description_zh"
                                    v-model="form.description_zh"
                                    rows="2"
                                    class="mt-1 block w-full rounded-xl border-gray-300 text-xs shadow-xs focus:border-brand-500 focus:ring-brand-500"
                                    placeholder="肉类将分发给偏远地区的贫困家庭。"
                                ></textarea>
                            </div>
                            <div>
                                <InputLabel for="description_ar" value="Deskripsi (Arabic)" class="!text-xs font-bold" />
                                <textarea
                                    id="description_ar"
                                    v-model="form.description_ar"
                                    rows="2"
                                    dir="rtl"
                                    class="mt-1 block w-full rounded-xl border-gray-300 text-xs shadow-xs focus:border-brand-500 focus:ring-brand-500"
                                    placeholder="سيتم توزيع اللحوم على المحتاجين في القرى النائية."
                                ></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Biaya Tambahan / Multi-Currency -->
                <div class="border-b border-gray-100 pb-5">
                    <h3 class="text-base font-bold text-gray-900">Biaya Tambahan Penyaluran (Jika Ada)</h3>
                    <p class="text-xs text-gray-500">Biaya pengiriman/operasional ke wilayah terkait (isi 0 jika gratis/sudah termasuk).</p>

                    <div class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-4">
                        <div>
                            <InputLabel for="fee_idr" value="Biaya IDR (Rp)" class="!text-xs font-bold" />
                            <TextInput
                                id="fee_idr"
                                v-model="form.fee_idr"
                                type="number"
                                min="0"
                                class="mt-1 block w-full !rounded-xl !text-xs"
                                required
                            />
                            <InputError class="mt-1" :message="form.errors.fee_idr" />
                        </div>
                        <div>
                            <InputLabel for="fee_usd" value="Biaya USD ($)" class="!text-xs font-bold" />
                            <TextInput
                                id="fee_usd"
                                v-model="form.fee_usd"
                                type="number"
                                min="0"
                                step="0.01"
                                class="mt-1 block w-full !rounded-xl !text-xs"
                            />
                        </div>
                        <div>
                            <InputLabel for="fee_cny" value="Biaya CNY (¥)" class="!text-xs font-bold" />
                            <TextInput
                                id="fee_cny"
                                v-model="form.fee_cny"
                                type="number"
                                min="0"
                                step="0.01"
                                class="mt-1 block w-full !rounded-xl !text-xs"
                            />
                        </div>
                        <div>
                            <InputLabel for="fee_sar" value="Biaya SAR (﷼)" class="!text-xs font-bold" />
                            <TextInput
                                id="fee_sar"
                                v-model="form.fee_sar"
                                type="number"
                                min="0"
                                step="0.01"
                                class="mt-1 block w-full !rounded-xl !text-xs"
                            />
                        </div>
                    </div>
                </div>

                <!-- 4. Pengaturan Tampilan & Status -->
                <div class="space-y-4">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <InputLabel for="order" value="Nomor Urutan Tampilan" class="!text-xs font-bold" />
                            <TextInput
                                id="order"
                                v-model="form.order"
                                type="number"
                                min="0"
                                class="mt-1 block w-full !rounded-xl !text-xs"
                            />
                            <InputError class="mt-1" :message="form.errors.order" />
                        </div>
                        <div class="flex items-center pt-6">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <Checkbox v-model:checked="form.is_active" />
                                <span class="text-xs font-semibold text-gray-700">Aktifkan opsi penyaluran ini</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-5">
                    <Link
                        :href="route('admin.distribusi.index')"
                        class="inline-flex items-center rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-xs font-bold text-gray-700 shadow-xs hover:bg-gray-50"
                    >
                        Batal
                    </Link>
                    <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing" class="!rounded-xl !text-xs !py-2.5">
                        {{ isEdit ? 'Simpan Perubahan' : 'Tambah Opsi Penyaluran' }}
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
