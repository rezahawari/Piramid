<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth?.user);

const isActive = (path) => {
    if (path === '/') return page.url === '/';
    if (path === '/layanan') return page.url.startsWith('/layanan') || page.url.startsWith('/katalog');
    if (path === '/transaksi') return page.url.startsWith('/transaksi');
    if (path === '/admin') return page.url.startsWith('/admin');
    return page.url === path;
};
</script>

<template>
    <!-- Floating Liquid Glass Mobile Snackbar / Bottom Navigation Bar -->
    <div class="fixed bottom-4 inset-x-0 z-50 flex justify-center px-4 pointer-events-none md:hidden">
        <nav
            class="pointer-events-auto flex items-center justify-around gap-1 w-full max-w-sm rounded-3xl p-1.5 backdrop-blur-2xl bg-slate-950/80 border border-white/20 shadow-[0_12px_40px_rgba(0,0,0,0.45)] ring-1 ring-white/10 text-white transition-all duration-300 transform-gpu"
        >
            <!-- 1. Logo Piramid Statis (Non-Clickable) -->
            <div class="flex items-center justify-center py-1.5 px-3 select-none">
                <div class="flex items-center gap-1.5 bg-white/10 rounded-2xl px-2.5 py-1.5 border border-white/15 shadow-inner">
                    <img
                        src="/images/logo.png"
                        alt="Logo Piramid"
                        class="h-6 w-auto object-contain"
                    />
                    <span class="font-script text-lg text-amber-300 leading-none">Piramid</span>
                </div>
            </div>

            <!-- 2. Layanan / Pemesanan -->
            <Link
                href="/layanan"
                class="flex flex-col items-center justify-center flex-1 py-2 px-1 rounded-2xl transition-all duration-200"
                :class="isActive('/layanan') ? 'bg-brand-500/80 text-white font-bold shadow-lg shadow-brand-500/30' : 'text-zinc-400 hover:text-white'"
            >
                <svg class="h-5 w-5 transition-transform" :class="isActive('/layanan') ? 'scale-110 text-white' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <span class="text-[10px] mt-0.5 tracking-tight">Layanan</span>
            </Link>

            <!-- 3. Riwayat Transaksi (Jika User Login) -->
            <Link
                v-if="user"
                href="/transaksi"
                class="flex flex-col items-center justify-center flex-1 py-2 px-1 rounded-2xl transition-all duration-200"
                :class="isActive('/transaksi') ? 'bg-white/15 text-amber-300 font-bold shadow-inner' : 'text-zinc-400 hover:text-white'"
            >
                <svg class="h-5 w-5 transition-transform" :class="isActive('/transaksi') ? 'scale-110 text-amber-300' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                <span class="text-[10px] mt-0.5 tracking-tight">Transaksi</span>
            </Link>

            <!-- 4. Admin Menu (Jika Role Admin) -->
            <Link
                v-if="user && user.role === 'admin'"
                href="/admin"
                class="flex flex-col items-center justify-center flex-1 py-2 px-1 rounded-2xl transition-all duration-200"
                :class="isActive('/admin') ? 'bg-white/15 text-amber-300 font-bold shadow-inner' : 'text-zinc-400 hover:text-white'"
            >
                <svg class="h-5 w-5 transition-transform" :class="isActive('/admin') ? 'scale-110 text-amber-300' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span class="text-[10px] mt-0.5 tracking-tight">Admin</span>
            </Link>

            <!-- 5. Akun / Keluar (Jika User Login) -->
            <Link
                v-if="user"
                href="/logout"
                method="post"
                as="button"
                class="flex flex-col items-center justify-center flex-1 py-2 px-1 rounded-2xl text-rose-300 hover:text-rose-200 transition-all duration-200"
            >
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span class="text-[10px] mt-0.5 tracking-tight">Keluar</span>
            </Link>

            <!-- 5. Masuk (Jika Guest / Belum Login) -->
            <Link
                v-else
                :href="route('login')"
                class="flex flex-col items-center justify-center flex-1 py-2 px-1 rounded-2xl transition-all duration-200"
                :class="isActive('/login') ? 'bg-white/15 text-amber-300 font-bold shadow-inner' : 'text-zinc-400 hover:text-white'"
            >
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                </svg>
                <span class="text-[10px] mt-0.5 tracking-tight">Masuk</span>
            </Link>
        </nav>
    </div>
</template>
