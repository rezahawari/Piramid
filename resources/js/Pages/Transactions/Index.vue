<script setup>
import StatusBadge from '@/Components/StatusBadge.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    transactions: Object,
});

const rupiah = (v) =>
    new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(v);

const tanggal = (iso) =>
    new Date(iso).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
</script>

<template>
    <Head title="Transaksi Saya" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Transaksi Saya</h2>
        </template>

        <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6">
            <div v-if="transactions.data.length" class="space-y-3">
                <Link
                    v-for="t in transactions.data"
                    :key="t.id"
                    :href="route('transactions.show', t.transaction_code)"
                    class="block rounded-lg bg-white p-4 shadow-sm transition hover:shadow"
                >
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div>
                            <p class="font-mono text-sm text-gray-500">{{ t.transaction_code }}</p>
                            <p class="font-semibold text-gray-900">
                                {{ t.product?.name }}
                                <span class="font-normal text-gray-500">· {{ t.service?.name }}</span>
                            </p>
                            <p class="text-sm text-gray-500">{{ tanggal(t.created_at) }} · {{ rupiah(t.total_amount) }}</p>
                        </div>
                        <div class="flex gap-2">
                            <StatusBadge :status="t.payment_status" />
                            <StatusBadge :status="t.status" />
                        </div>
                    </div>
                </Link>

                <!-- Pagination -->
                <div v-if="transactions.links.length > 3" class="flex flex-wrap gap-1 pt-4">
                    <template v-for="(link, i) in transactions.links" :key="i">
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            class="rounded px-3 py-1 text-sm"
                            :class="link.active ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'"
                            v-html="link.label"
                        />
                        <span v-else class="px-3 py-1 text-sm text-gray-400" v-html="link.label" />
                    </template>
                </div>
            </div>

            <div v-else class="rounded-lg bg-white p-8 text-center shadow-sm">
                <p class="text-gray-500">Belum ada transaksi.</p>
                <Link href="/" class="mt-2 inline-block text-indigo-600 hover:underline">Lihat layanan</Link>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
