<script setup>
import { Link } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';

const props = defineProps({
    products: { type: Array, default: () => [] },
    intervalMs: { type: Number, default: 4500 },
});

const animals = [
    { id: 'sapi', name: 'Sapi', icon: '/assets/images/animals/sapi.png', keywords: ['sapi'] },
    { id: 'kambing', name: 'Kambing', icon: '/assets/images/animals/kambing.png', keywords: ['kambing'] },
    { id: 'domba', name: 'Domba', icon: '/assets/images/animals/domba.png', keywords: ['domba'] },
    { id: 'unta', name: 'Unta', icon: '/assets/images/animals/domba.png', keywords: ['unta'] },
];

const active = ref(0);
let timer = null;

const rupiah = (v) =>
    new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(v);

const animalCards = computed(() =>
    animals.map((animal) => {
        const matched = props.products.filter((p) =>
            animal.keywords.some((k) => p.name.toLowerCase().includes(k)),
        );
        const sorted = [...matched].sort((a, b) => a.price - b.price);
        const cheapest = sorted[0];
        const weights = matched.map((p) => p.weight_estimate_kg).filter(Boolean);
        const minW = weights.length ? Math.min(...weights) : null;
        const maxW = weights.length ? Math.max(...weights) : null;

        let weightLabel = null;
        if (minW && maxW && minW !== maxW) {
            weightLabel = `${Math.round(minW)} kg - ${Math.round(maxW)} kg`;
        } else if (minW) {
            weightLabel = `±${Math.round(minW)} kg`;
        }

        return {
            ...animal,
            price: cheapest?.price ?? null,
            slug: cheapest?.slug ?? null,
            weightLabel,
        };
    }),
);

const selected = computed(() => animalCards.value[active.value] ?? animalCards.value[0]);

const orderHref = computed(() =>
    selected.value?.slug ? `/layanan/qurban/produk/${selected.value.slug}` : '/layanan/qurban',
);

const goTo = (i) => {
    active.value = (i + animals.length) % animals.length;
};

const next = () => goTo(active.value + 1);

const start = () => {
    stop();
    timer = setInterval(next, props.intervalMs);
};

const stop = () => {
    if (timer) clearInterval(timer);
    timer = null;
};

const select = (i) => {
    goTo(i);
    start();
};

onMounted(start);
onBeforeUnmount(stop);
</script>

<template>
    <div class="grid gap-10 lg:grid-cols-2 lg:items-stretch" @mouseenter="stop" @mouseleave="start">
        <!-- Kolom kiri: judul + ilustrasi besar (berubah sesuai pilihan) -->
        <div class="flex flex-col gap-6">
            <slot name="header" />
            <div class="relative min-h-[300px] flex-1 overflow-hidden rounded-2xl sm:min-h-[420px]">
                <img
                    v-for="(animal, i) in animalCards"
                    :key="animal.id"
                    :src="animal.icon"
                    :alt="`Ilustrasi ${animal.name}`"
                    class="absolute inset-0 h-full w-full object-cover object-top transition-all duration-500"
                    :class="
                        i === active
                            ? 'scale-90 opacity-100'
                            : 'pointer-events-none scale-85 opacity-0'
                    "
                />
            </div>
        </div>

        <!-- Kartu pilihan hewan -->
        <div class="flex flex-col gap-4">
            <button
                v-for="(animal, i) in animalCards"
                :key="animal.id"
                type="button"
                :aria-pressed="i === active"
                :aria-label="`Pilih ${animal.name}`"
                class="group flex items-stretch overflow-hidden rounded-xl text-left transition hover:-translate-y-0.5 hover:shadow-lg"
                @click="select(i)"
            >
                <span
                    class="flex w-28 shrink-0 items-center justify-center bg-white p-3 transition"
                    :class="
                        i === active
                            ? 'border-b-4 border-l-4 border-brand-500'
                            : 'border-b-4 border-l-4 border-transparent'
                    "
                >
                    <img
                        :src="animal.icon"
                        :alt="animal.name"
                        class="h-20 w-20 object-contain"
                    />
                </span>
                <span class="flex flex-1 flex-col justify-center gap-0.5 bg-night-soft px-5 py-4 text-white">
                    <span class="text-lg font-bold">{{ animal.name }}</span>
                    <span v-if="animal.weightLabel" class="text-xs text-zinc-400">
                        {{ animal.weightLabel }}
                    </span>
                    <span class="mt-1 text-xs text-zinc-400">Mulai dari</span>
                    <span
                        class="text-lg font-extrabold"
                        :class="i === active ? 'text-brand-300' : 'text-white'"
                    >
                        {{ animal.price ? rupiah(animal.price) : 'Hubungi kami' }}
                    </span>
                </span>
            </button>

            <div class="flex justify-end pt-2">
                <Link
                    :href="orderHref"
                    class="inline-flex items-center gap-2 rounded-md bg-sun-400 px-6 py-3 font-bold text-cocoa transition hover:bg-sun-500"
                >
                    Pesan Sekarang
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 17L17 7M17 7H7M17 7v10" />
                    </svg>
                </Link>
            </div>
        </div>
    </div>
</template>
