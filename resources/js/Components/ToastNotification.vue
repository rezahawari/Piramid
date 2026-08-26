<script setup>
import { usePage } from '@inertiajs/vue3';
import { computed, onMounted, ref, watch } from 'vue';

const page = usePage();
const visible = ref(false);
const toast = ref({
    type: 'success', // 'success' | 'error' | 'warning' | 'info'
    title: '',
    message: '',
});
let timer = null;

const showToast = (type, title, message, duration = 3000) => {
    toast.value = { type, title, message };
    visible.value = true;
    if (timer) clearTimeout(timer);
    timer = setTimeout(() => {
        visible.value = false;
    }, duration);
};

// Global event listener & window exposure
onMounted(() => {
    window.piramidToast = {
        success: (title, msg, dur) => showToast('success', title, msg, dur),
        error: (title, msg, dur) => showToast('error', title, msg, dur),
        warning: (title, msg, dur) => showToast('warning', title, msg, dur),
        info: (title, msg, dur) => showToast('info', title, msg, dur),
    };

    // Dengarkan flash messages otomatis dari backend Laravel Inertia
    checkFlashMessages();
});

const checkFlashMessages = () => {
    const flash = page.props.flash;
    if (flash?.success) {
        showToast('success', 'Berhasil!', flash.success);
    } else if (flash?.error) {
        showToast('error', 'Terjadi Kendala', flash.error);
    } else if (flash?.warning) {
        showToast('warning', 'Pemberitahuan', flash.warning);
    }
};

watch(() => page.props.flash, () => {
    checkFlashMessages();
}, { deep: true });

const close = () => {
    visible.value = false;
};
</script>

<template>
    <!-- Liquid Glass Global Toast Notification -->
    <transition
        enter-active-class="transition ease-out duration-300 transform"
        enter-from-class="opacity-0 -translate-y-4 scale-95"
        enter-to-class="opacity-100 translate-y-0 scale-100"
        leave-active-class="transition ease-in duration-200 transform"
        leave-from-class="opacity-100 translate-y-0 scale-100"
        leave-to-class="opacity-0 -translate-y-4 scale-95"
    >
        <div
            v-if="visible"
            class="fixed top-5 inset-x-0 z-[100] flex justify-center px-4 pointer-events-none"
        >
            <div
                class="pointer-events-auto flex items-center gap-3 rounded-2xl p-3.5 backdrop-blur-2xl bg-slate-950/90 border shadow-2xl ring-1 ring-white/10 text-white max-w-md w-full relative overflow-hidden"
                :class="{
                    'border-emerald-500/50 shadow-emerald-950/40': toast.type === 'success',
                    'border-rose-500/50 shadow-rose-950/40': toast.type === 'error',
                    'border-amber-500/50 shadow-amber-950/40': toast.type === 'warning',
                    'border-sky-500/50 shadow-sky-950/40': toast.type === 'info',
                }"
            >
                <!-- Ambient Glow Effect -->
                <div
                    class="absolute -top-6 -right-6 w-20 h-20 rounded-full blur-xl pointer-events-none opacity-40"
                    :class="{
                        'bg-emerald-400': toast.type === 'success',
                        'bg-rose-400': toast.type === 'error',
                        'bg-amber-400': toast.type === 'warning',
                        'bg-sky-400': toast.type === 'info',
                    }"
                ></div>

                <!-- Icon -->
                <div
                    class="flex h-9 w-9 items-center justify-center rounded-xl shrink-0 shadow-inner"
                    :class="{
                        'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30': toast.type === 'success',
                        'bg-rose-500/20 text-rose-400 border border-rose-500/30': toast.type === 'error',
                        'bg-amber-500/20 text-amber-400 border border-amber-500/30': toast.type === 'warning',
                        'bg-sky-500/20 text-sky-400 border border-sky-500/30': toast.type === 'info',
                    }"
                >
                    <!-- Success Icon -->
                    <svg v-if="toast.type === 'success'" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                    <!-- Error Icon -->
                    <svg v-else-if="toast.type === 'error'" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <!-- Warning Icon -->
                    <svg v-else-if="toast.type === 'warning'" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <!-- Info Icon -->
                    <svg v-else class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>

                <!-- Text Content -->
                <div class="flex-1 min-w-0 pr-1">
                    <h5 class="text-xs font-black tracking-tight text-white leading-snug">
                        {{ toast.title }}
                    </h5>
                    <p v-if="toast.message" class="text-[11px] text-zinc-300 font-normal mt-0.5 leading-relaxed truncate">
                        {{ toast.message }}
                    </p>
                </div>

                <!-- Close Button -->
                <button
                    type="button"
                    @click="close"
                    class="h-6 w-6 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center text-zinc-400 hover:text-white transition shrink-0"
                >
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </transition>
</template>
