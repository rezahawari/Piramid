<script setup>
import { computed } from 'vue';

const props = defineProps({
    // transactions.status value: menunggu|dibayar|hewan_disiapkan|tersembelih|didistribusikan
    // or payment_status value: pending|paid|rejected|expired|cancelled
    status: { type: String, required: true },
});

const labels = {
    menunggu: 'Menunggu',
    dibayar: 'Dibayar',
    hewan_disiapkan: 'Hewan Disiapkan',
    tersembelih: 'Tersembelih',
    didistribusikan: 'Didistribusikan',
    pending: 'Menunggu Pembayaran',
    paid: 'Lunas',
    rejected: 'Ditolak',
    expired: 'Kedaluwarsa',
    cancelled: 'Dibatalkan',
};

const colors = {
    menunggu: 'bg-gray-100 text-gray-700',
    dibayar: 'bg-blue-100 text-blue-700',
    hewan_disiapkan: 'bg-amber-100 text-amber-700',
    tersembelih: 'bg-purple-100 text-purple-700',
    didistribusikan: 'bg-green-100 text-green-700',
    pending: 'bg-yellow-100 text-yellow-700',
    paid: 'bg-green-100 text-green-700',
    rejected: 'bg-red-100 text-red-700',
    expired: 'bg-gray-100 text-gray-500',
    cancelled: 'bg-gray-100 text-gray-500',
};

const label = computed(() => labels[props.status] ?? props.status);
const color = computed(() => colors[props.status] ?? 'bg-gray-100 text-gray-700');
</script>

<template>
    <span
        class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
        :class="color"
    >
        {{ label }}
    </span>
</template>
