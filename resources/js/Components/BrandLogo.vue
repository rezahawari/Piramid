<script setup>
defineProps({
    // 'mark' = kotak hijau saja, 'full' = kotak + wordmark
    variant: { type: String, default: 'mark' },
    dark: { type: Boolean, default: false },
});
</script>

<template>
    <span class="inline-flex items-center gap-2">
        <img
            src="/images/logo.png"
            alt="Logo Piramid"
            class="h-12 w-auto object-contain"
        />
        <span
            v-if="variant === 'full'"
            class="text-2xl font-extrabold tracking-tight"
            :class="dark ? 'text-white' : 'text-night-soft'"
        >
            Piramid
        </span>
    </span>
</template>
