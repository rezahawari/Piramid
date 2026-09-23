<script setup>
import { currentLocale, setLocale, SUPPORTED_LOCALES } from '@/i18n';
import { onMounted, onUnmounted, ref } from 'vue';

const isOpen = ref(false);
const dropdownRef = ref(null);

const toggleDropdown = () => {
    isOpen.value = !isOpen.value;
};

const selectLanguage = (code) => {
    setLocale(code);
    isOpen.value = false;
};

const handleClickOutside = (event) => {
    if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
        isOpen.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>

<template>
    <div ref="dropdownRef" class="relative inline-block text-left">
        <!-- Trigger Button -->
        <button
            type="button"
            @click.stop="toggleDropdown"
            class="flex items-center gap-1.5 rounded-full border border-zinc-200 bg-white/90 px-3 py-1.5 text-xs font-semibold text-zinc-700 shadow-sm backdrop-blur hover:border-brand-500 hover:text-brand-600 transition"
            aria-haspopup="true"
            :aria-expanded="isOpen"
        >
            <span class="text-sm leading-none">
                {{ SUPPORTED_LOCALES.find(l => l.code === currentLocale)?.flag }}
            </span>
            <span class="uppercase font-bold tracking-wider text-[11px]">
                {{ currentLocale }}
            </span>
            <svg
                class="h-3.5 w-3.5 text-zinc-400 transition-transform duration-200"
                :class="isOpen ? 'rotate-180 text-brand-500' : ''"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <!-- Dropdown Menu -->
        <transition
            enter-active-class="transition duration-150 ease-out"
            enter-from-class="transform scale-95 opacity-0"
            enter-to-class="transform scale-100 opacity-100"
            leave-active-class="transition duration-100 ease-in"
            leave-from-class="transform scale-100 opacity-100"
            leave-to-class="transform scale-95 opacity-0"
        >
            <div
                v-if="isOpen"
                class="absolute right-0 z-50 mt-2 w-44 origin-top-right rounded-2xl border border-zinc-100 bg-white/95 p-1.5 shadow-2xl backdrop-blur-xl ring-1 ring-black/5"
            >
                <div class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-zinc-400">
                    Pilih Bahasa / Language
                </div>
                <button
                    v-for="locale in SUPPORTED_LOCALES"
                    :key="locale.code"
                    type="button"
                    @click="selectLanguage(locale.code)"
                    class="flex w-full items-center justify-between gap-2 rounded-xl px-2.5 py-2 text-left text-xs font-medium transition cursor-pointer"
                    :class="currentLocale === locale.code ? 'bg-brand-50 text-brand-600 font-bold' : 'text-zinc-700 hover:bg-zinc-50'"
                >
                    <span class="flex items-center gap-2">
                        <span class="text-sm leading-none">{{ locale.flag }}</span>
                        <span>{{ locale.name }}</span>
                    </span>
                    <svg
                        v-if="currentLocale === locale.code"
                        class="h-4 w-4 text-brand-500"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                </button>
            </div>
        </transition>
    </div>
</template>
