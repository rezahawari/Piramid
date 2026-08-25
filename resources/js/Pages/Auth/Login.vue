<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const showPassword = ref(false);

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Masuk ke Akun - Piramid Qurban & Aqiqah" />

    <div class="min-h-screen bg-slate-950 flex flex-col justify-center relative overflow-hidden font-sans select-none sm:select-auto">
        <!-- Background Ambient Glow & Patterns -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -left-40 w-96 h-96 bg-brand-600/20 rounded-full blur-3xl"></div>
            <div class="absolute top-1/2 -right-40 w-96 h-96 bg-amber-500/15 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-40 left-1/3 w-96 h-96 bg-brand-800/25 rounded-full blur-3xl"></div>
            <div class="absolute inset-0 bg-[radial-gradient(#ffffff0a_1px,transparent_1px)] [background-size:20px_20px] opacity-40"></div>
        </div>

        <div class="w-full max-w-5xl mx-auto p-4 sm:p-6 lg:p-8 relative z-10">
            <div class="grid lg:grid-cols-12 rounded-3xl overflow-hidden shadow-2xl border border-white/10 bg-white/5 backdrop-blur-xl">
                
                <!-- Left Banner / Branding Column (Desktop only) -->
                <div class="hidden lg:flex lg:col-span-5 relative flex-col justify-between p-10 bg-gradient-to-br from-brand-950/90 via-brand-900/80 to-slate-950/90 border-r border-white/10 text-white">
                    <img
                        src="/images/hero-slider/g1.jpeg"
                        alt="Background Piramid"
                        class="absolute inset-0 w-full h-full object-cover mix-blend-overlay opacity-25 pointer-events-none"
                    />

                    <!-- Top Logo -->
                    <div class="relative z-10">
                        <Link href="/" class="inline-flex items-center gap-3 group">
                            <div class="h-12 w-12 rounded-2xl bg-white/10 p-2 border border-white/20 shadow-inner flex items-center justify-center transition group-hover:scale-105">
                                <img src="/images/logo.png" alt="Logo Piramid" class="h-8 w-auto object-contain" />
                            </div>
                            <div>
                                <span class="font-script text-3xl tracking-wide text-amber-300 block leading-tight">Piramid</span>
                                <span class="text-[10px] uppercase font-bold tracking-widest text-white/70 block">Qurban & Aqiqah</span>
                            </div>
                        </Link>
                    </div>

                    <!-- Center Feature Highlight -->
                    <div class="relative z-10 space-y-6 my-auto">
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-brand-500/20 border border-brand-400/30 text-amber-300 text-xs font-semibold">
                            <span class="flex h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            Platform Amanah & Sesuai Syariat
                        </div>
                        <h2 class="text-2xl font-black leading-snug tracking-tight text-white">
                            Ibadah Qurban & Aqiqah Lebih Mudah, Transparan, & Terpercaya.
                        </h2>
                        <ul class="space-y-3 text-xs text-zinc-300">
                            <li class="flex items-center gap-2.5">
                                <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-500/20 text-emerald-400 font-bold">✓</span>
                                Dokumentasi video & foto penyembelihan langsung
                            </li>
                            <li class="flex items-center gap-2.5">
                                <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-500/20 text-emerald-400 font-bold">✓</span>
                                Sertifikat resmi & pelaporan distribusi amanah
                            </li>
                            <li class="flex items-center gap-2.5">
                                <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-500/20 text-emerald-400 font-bold">✓</span>
                                Pembayaran instan Virtual Account, QRIS & Transfer
                            </li>
                        </ul>
                    </div>

                    <!-- Bottom Tagline -->
                    <div class="relative z-10 pt-6 border-t border-white/10 text-[11px] text-zinc-400">
                        &copy; {{ new Date().getFullYear() }} Piramid Qurban. Seluruh Hak Cipta Dilindungi.
                    </div>
                </div>

                <!-- Right Form Column (Mobile & Desktop) -->
                <div class="lg:col-span-7 bg-white p-6 sm:p-10 lg:p-12 flex flex-col justify-center">
                    
                    <!-- Mobile Header Logo -->
                    <div class="flex lg:hidden items-center justify-between pb-6 mb-6 border-b border-gray-100">
                        <Link href="/" class="flex items-center gap-2.5">
                            <img src="/images/logo.png" alt="Logo Piramid" class="h-9 w-auto object-contain" />
                            <span class="font-script text-2xl text-brand-900 leading-none">Piramid</span>
                        </Link>
                        <span class="text-[10px] font-bold tracking-wider uppercase px-2.5 py-1 bg-brand-50 text-brand-700 rounded-full border border-brand-100">
                            Mobile App
                        </span>
                    </div>

                    <!-- Title Header -->
                    <div class="mb-8">
                        <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">
                            Selamat Datang Kembali 👋
                        </h1>
                        <p class="mt-1.5 text-xs sm:text-sm text-gray-500">
                            Silakan masuk dengan akun Anda untuk melanjutkan pemesanan & memantau transaksi.
                        </p>
                    </div>

                    <div v-if="status" class="mb-6 rounded-xl bg-emerald-50 border border-emerald-200 p-4 text-xs font-semibold text-emerald-800 flex items-center gap-2">
                        <svg class="h-4 w-4 shrink-0 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ status }}</span>
                    </div>

                    <!-- Login Form -->
                    <form @submit.prevent="submit" class="space-y-5">
                        
                        <!-- Email Input -->
                        <div>
                            <InputLabel for="email" value="Alamat Email" class="!text-xs font-bold text-gray-700" />
                            <div class="relative mt-1.5">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                    </svg>
                                </span>
                                <TextInput
                                    id="email"
                                    type="email"
                                    class="block w-full !pl-10 !py-3 !rounded-xl !text-sm border-gray-200 focus:border-brand-500 focus:ring-brand-500/20 shadow-xs transition"
                                    v-model="form.email"
                                    placeholder="nama@email.com"
                                    required
                                    autofocus
                                    autocomplete="username"
                                />
                            </div>
                            <InputError class="mt-1.5" :message="form.errors.email" />
                        </div>

                        <!-- Password Input -->
                        <div>
                            <div class="flex items-center justify-between">
                                <InputLabel for="password" value="Kata Sandi" class="!text-xs font-bold text-gray-700" />
                                <Link
                                    v-if="canResetPassword"
                                    :href="route('password.request')"
                                    class="text-xs font-semibold text-brand-600 hover:text-brand-700 transition"
                                >
                                    Lupa kata sandi?
                                </Link>
                            </div>
                            <div class="relative mt-1.5">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </span>
                                <TextInput
                                    id="password"
                                    :type="showPassword ? 'text' : 'password'"
                                    class="block w-full !pl-10 !pr-10 !py-3 !rounded-xl !text-sm border-gray-200 focus:border-brand-500 focus:ring-brand-500/20 shadow-xs transition"
                                    v-model="form.password"
                                    placeholder="••••••••"
                                    required
                                    autocomplete="current-password"
                                />
                                <button
                                    type="button"
                                    @click="showPassword = !showPassword"
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 transition"
                                    tabindex="-1"
                                >
                                    <svg v-if="!showPassword" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg v-else class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                    </svg>
                                </button>
                            </div>
                            <InputError class="mt-1.5" :message="form.errors.password" />
                        </div>

                        <!-- Remember Me Checkbox -->
                        <div class="flex items-center justify-between pt-1">
                            <label class="flex items-center cursor-pointer select-none">
                                <Checkbox name="remember" v-model:checked="form.remember" class="!rounded !border-gray-300 text-brand-600 focus:ring-brand-500" />
                                <span class="ms-2 text-xs font-semibold text-gray-600">Ingat sesi saya</span>
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-2">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="w-full inline-flex justify-center items-center gap-2 rounded-xl bg-gradient-to-r from-brand-700 via-brand-600 to-brand-700 hover:from-brand-800 hover:to-brand-800 text-white font-bold py-3.5 px-6 text-sm shadow-lg shadow-brand-700/25 transition active:scale-[0.99] disabled:opacity-50 cursor-pointer"
                            >
                                <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>{{ form.processing ? 'Memproses Masuk...' : 'Masuk Sekarang' }}</span>
                                <svg v-if="!form.processing" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </button>
                        </div>

                        <!-- Register Link -->
                        <div class="pt-4 text-center border-t border-gray-100">
                            <p class="text-xs text-gray-500">
                                Belum memiliki akun Piramid?
                                <Link
                                    :href="route('register')"
                                    class="font-bold text-brand-600 hover:text-brand-700 transition ml-1"
                                >
                                    Daftar Akun Baru
                                </Link>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

