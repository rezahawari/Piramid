<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const showPassword = ref(false);

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Daftar Akun Baru - Piramid Qurban & Aqiqah" />

    <div class="min-h-screen bg-slate-950 flex flex-col justify-center relative overflow-hidden font-sans select-none sm:select-auto">
        <!-- Background Glow -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -right-40 w-96 h-96 bg-brand-600/20 rounded-full blur-3xl"></div>
            <div class="absolute top-1/2 -left-40 w-96 h-96 bg-amber-500/15 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-40 right-1/3 w-96 h-96 bg-brand-800/25 rounded-full blur-3xl"></div>
            <div class="absolute inset-0 bg-[radial-gradient(#ffffff0a_1px,transparent_1px)] [background-size:20px_20px] opacity-40"></div>
        </div>

        <div class="w-full max-w-5xl mx-auto p-4 sm:p-6 lg:p-8 relative z-10">
            <div class="grid lg:grid-cols-12 rounded-3xl overflow-hidden shadow-2xl border border-white/10 bg-white/5 backdrop-blur-xl">
                
                <!-- Left Banner Column (Desktop only) -->
                <div class="hidden lg:flex lg:col-span-5 relative flex-col justify-between p-10 bg-gradient-to-br from-brand-950/90 via-brand-900/80 to-slate-950/90 border-r border-white/10 text-white">
                    <img
                        src="/images/hero-slider/8.jpg"
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

                    <!-- Center Info -->
                    <div class="relative z-10 space-y-6 my-auto">
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-brand-500/20 border border-brand-400/30 text-amber-300 text-xs font-semibold">
                            <span class="flex h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            Gabung Bersama Ribuan Shohibul
                        </div>
                        <h2 class="text-2xl font-black leading-snug tracking-tight text-white">
                            Mulai Perjalanan Ibadah yang Penuh Berkah Bersama Piramid.
                        </h2>
                        <p class="text-xs text-zinc-300 leading-relaxed">
                            Daftarkan akun sekarang untuk kemudahan pemesanan, dokumentasi berkala, serta sertifikat resmi atas nama Anda dan keluarga.
                        </p>
                    </div>

                    <!-- Bottom Tagline -->
                    <div class="relative z-10 pt-6 border-t border-white/10 text-[11px] text-zinc-400">
                        &copy; {{ new Date().getFullYear() }} Piramid Qurban. Seluruh Hak Cipta Dilindungi.
                    </div>
                </div>

                <!-- Right Form Column -->
                <div class="lg:col-span-7 bg-white p-6 sm:p-10 lg:p-12 flex flex-col justify-center">
                    
                    <!-- Mobile Header Logo -->
                    <div class="flex lg:hidden items-center justify-between pb-6 mb-6 border-b border-gray-100">
                        <Link href="/" class="flex items-center gap-2.5">
                            <img src="/images/logo.png" alt="Logo Piramid" class="h-9 w-auto object-contain" />
                            <span class="font-script text-2xl text-brand-900 leading-none">Piramid</span>
                        </Link>
                        <span class="text-[10px] font-bold tracking-wider uppercase px-2.5 py-1 bg-brand-50 text-brand-700 rounded-full border border-brand-100">
                            Pendaftaran
                        </span>
                    </div>

                    <!-- Header -->
                    <div class="mb-6">
                        <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">
                            Daftar Akun Baru ✨
                        </h1>
                        <p class="mt-1 text-xs sm:text-sm text-gray-500">
                            Isi data diri Anda di bawah ini untuk membuat akun baru.
                        </p>
                    </div>

                    <!-- Form -->
                    <form @submit.prevent="submit" class="space-y-4">
                        
                        <!-- Nama Lengkap -->
                        <div>
                            <InputLabel for="name" value="Nama Lengkap" class="!text-xs font-bold text-gray-700" />
                            <div class="relative mt-1">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </span>
                                <TextInput
                                    id="name"
                                    type="text"
                                    class="block w-full !pl-10 !py-2.5 !rounded-xl !text-sm border-gray-200 focus:border-brand-500 focus:ring-brand-500/20 shadow-xs transition"
                                    v-model="form.name"
                                    placeholder="Contoh: H. Ahmad Fulan"
                                    required
                                    autofocus
                                    autocomplete="name"
                                />
                            </div>
                            <InputError class="mt-1" :message="form.errors.name" />
                        </div>

                        <!-- Email -->
                        <div>
                            <InputLabel for="email" value="Alamat Email" class="!text-xs font-bold text-gray-700" />
                            <div class="relative mt-1">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                    </svg>
                                </span>
                                <TextInput
                                    id="email"
                                    type="email"
                                    class="block w-full !pl-10 !py-2.5 !rounded-xl !text-sm border-gray-200 focus:border-brand-500 focus:ring-brand-500/20 shadow-xs transition"
                                    v-model="form.email"
                                    placeholder="nama@email.com"
                                    required
                                    autocomplete="username"
                                />
                            </div>
                            <InputError class="mt-1" :message="form.errors.email" />
                        </div>

                        <!-- Password -->
                        <div>
                            <InputLabel for="password" value="Kata Sandi" class="!text-xs font-bold text-gray-700" />
                            <div class="relative mt-1">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </span>
                                <TextInput
                                    id="password"
                                    :type="showPassword ? 'text' : 'password'"
                                    class="block w-full !pl-10 !pr-10 !py-2.5 !rounded-xl !text-sm border-gray-200 focus:border-brand-500 focus:ring-brand-500/20 shadow-xs transition"
                                    v-model="form.password"
                                    placeholder="Minimal 8 karakter"
                                    required
                                    autocomplete="new-password"
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
                            <InputError class="mt-1" :message="form.errors.password" />
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <InputLabel for="password_confirmation" value="Konfirmasi Kata Sandi" class="!text-xs font-bold text-gray-700" />
                            <div class="relative mt-1">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                </span>
                                <TextInput
                                    id="password_confirmation"
                                    :type="showPassword ? 'text' : 'password'"
                                    class="block w-full !pl-10 !py-2.5 !rounded-xl !text-sm border-gray-200 focus:border-brand-500 focus:ring-brand-500/20 shadow-xs transition"
                                    v-model="form.password_confirmation"
                                    placeholder="Ulangi kata sandi"
                                    required
                                    autocomplete="new-password"
                                />
                            </div>
                            <InputError class="mt-1" :message="form.errors.password_confirmation" />
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-3">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="w-full inline-flex justify-center items-center gap-2 rounded-xl bg-gradient-to-r from-brand-700 via-brand-600 to-brand-700 hover:from-brand-800 hover:to-brand-800 text-white font-bold py-3.5 px-6 text-sm shadow-lg shadow-brand-700/25 transition active:scale-[0.99] disabled:opacity-50 cursor-pointer"
                            >
                                <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>{{ form.processing ? 'Mendaftarkan Akun...' : 'Daftar Sekarang' }}</span>
                            </button>
                        </div>

                        <!-- Login Link -->
                        <div class="pt-3 text-center border-t border-gray-100">
                            <p class="text-xs text-gray-500">
                                Sudah memiliki akun?
                                <Link
                                    :href="route('login')"
                                    class="font-bold text-brand-600 hover:text-brand-700 transition ml-1"
                                >
                                    Masuk di Sini
                                </Link>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

