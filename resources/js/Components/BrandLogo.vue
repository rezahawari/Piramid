<script setup>
defineProps({
    // 'mark' = kotak hijau saja, 'full' = kotak + wordmark
    variant: { type: String, default: 'mark' },
    dark: { type: Boolean, default: false },
});
</script>

<template>
    <span class="inline-flex items-center gap-2">
        <span
            class="flex h-8 w-8 items-center justify-center rounded-md bg-brand-500"
            aria-hidden="true"
        >
            <svg viewBox="0 0 24 24" class="h-5 w-5 fill-white">
                <path d="M4 4h10l-6 7h6L4 20V4z" />
            </svg>
        </span>
        <span
            v-if="variant === 'full'"
            class="text-2xl font-extrabold tracking-tight"
            :class="dark ? 'text-white' : 'text-night-soft'"
        >
            Piramid
        </span>
    </span>
</template>
