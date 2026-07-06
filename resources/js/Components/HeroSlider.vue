<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue';

const props = defineProps({
    images: { type: Array, required: true },
    intervalMs: { type: Number, default: 4000 },
});

const active = ref(0);
let timer = null;

const goTo = (i) => {
    active.value = (i + props.images.length) % props.images.length;
};
const next = () => goTo(active.value + 1);
const prev = () => goTo(active.value - 1);

const start = () => {
    stop();
    timer = setInterval(next, props.intervalMs);
};
const stop = () => {
    if (timer) clearInterval(timer);
    timer = null;
};

onMounted(start);
onBeforeUnmount(stop);
</script>

<template>
    <div class="w-full" @mouseenter="stop" @mouseleave="start">
        <div class="relative aspect-[4/3] w-full overflow-hidden rounded-2xl shadow-md">
            <img
                v-for="(src, i) in images"
                :key="src"
                :src="src"
                alt="Dokumentasi hewan qurban & aqiqah Piramid"
                class="absolute inset-0 h-full w-full object-cover transition-opacity duration-700"
                :class="i === active ? 'opacity-100' : 'opacity-0'"
            />
        </div>
        <div class="mt-4 flex items-center justify-center gap-4">
            <button
                type="button"
                aria-label="Sebelumnya"
                @click="prev"
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full border border-zinc-200 text-zinc-500 transition hover:border-brand-500 hover:text-brand-500"
            >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            </button>
            <div class="flex items-center gap-1.5">
                <button
                    v-for="(src, i) in images"
                    :key="`dot-${src}`"
                    type="button"
                    :aria-label="`Ke slide ${i + 1}`"
                    @click="goTo(i)"
                    class="h-2 rounded-full transition-all"
                    :class="i === active ? 'w-6 bg-brand-500' : 'w-2 bg-zinc-200 hover:bg-brand-200'"
                />
            </div>
            <button
                type="button"
                aria-label="Berikutnya"
                @click="next"
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full border border-zinc-200 text-zinc-500 transition hover:border-brand-500 hover:text-brand-500"
            >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </button>
        </div>
    </div>
</template>
