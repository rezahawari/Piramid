<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const page = usePage();
const showMoreMenu = ref(false);

const isActive = (path) => {
    if (path === '/admin') return page.url === '/admin';
    return page.url.startsWith(path);
};

const navItems = [
    {
        label: 'Dashboard',
        href: '/admin',
        icon: 'dashboard',
    },
    {
        label: 'Transaksi',
        href: '/admin/transaksi',
        icon: 'transactions',
    },
    {
        label: 'Produk',
        href: '/admin/produk',
        icon: 'products',
    },
    {
        label: 'Layanan',
        href: '/admin/layanan',
        icon: 'services',
    },
];

const secondaryNav = [
    { label: 'Galeri & Edukasi', href: '/admin/galeri', icon: 'gallery' },
    { label: 'Manajemen Pengguna', href: '/admin/users', icon: 'users' },
    { label: 'Lihat Web Publik', href: '/layanan', icon: 'public' },
];
</script>

<template>
    <!-- Floating Liquid Glass Mobile Snackbar for Admin -->
    <div class="fixed bottom-3 inset-x-0 z-50 flex justify-center px-3 pointer-events-none md:hidden">
        <nav
            class="pointer-events-auto flex items-center justify-around gap-0.5 w-full max-w-sm rounded-2xl p-1 backdrop-blur-2xl bg-slate-950/85 border border-white/20 shadow-[0_10px_35px_rgba(0,0,0,0.5)] ring-1 ring-white/10 text-white relative transition-all duration-300 transform-gpu"
        >
            <!-- 1. Logo Statis Brand -->
            <div class="flex items-center justify-center py-1 px-1.5 select-none shrink-0">
                <div class="flex items-center gap-1 bg-amber-400/15 rounded-xl px-1.5 py-1 border border-amber-300/25">
                    <img
                        src="/images/logo.png"
                        alt="Logo"
                        class="h-4 w-auto object-contain"
                    />
                    <span class="font-bold text-[9px] text-amber-300 uppercase tracking-tight">Adm</span>
                </div>
            </div>

            <!-- 2. Main Navigation Items -->
            <Link
                v-for="item in navItems"
                :key="item.href"
                :href="item.href"
                class="flex flex-col items-center justify-center flex-1 py-1.5 px-0.5 rounded-xl transition-all duration-200"
                :class="isActive(item.href) ? 'bg-brand-500/90 text-white font-bold shadow-md shadow-brand-500/30' : 'text-zinc-400 hover:text-white'"
            >
                <!-- Dashboard Icon -->
                <svg v-if="item.icon === 'dashboard'" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>

                <!-- Transactions Icon -->
                <svg v-else-if="item.icon === 'transactions'" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>

                <!-- Products Icon -->
                <svg v-else-if="item.icon === 'products'" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>

                <!-- Services Icon -->
                <svg v-else class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>

                <span class="text-[8.5px] mt-0.5 tracking-tight font-medium">{{ item.label }}</span>
            </Link>

            <!-- 3. More Menu Trigger -->
            <button
                type="button"
                @click="showMoreMenu = !showMoreMenu"
                class="flex flex-col items-center justify-center py-1.5 px-1.5 rounded-xl transition-all duration-200"
                :class="showMoreMenu || isActive('/admin/galeri') || isActive('/admin/users') ? 'bg-white/15 text-amber-300 font-bold' : 'text-zinc-400 hover:text-white'"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <span class="text-[8.5px] mt-0.5 tracking-tight font-medium">Menu</span>
            </button>
        </nav>
    </div>

    <!-- More Drawer / Popup Modal Liquid Glass -->
    <transition
        enter-active-class="transition ease-out duration-250 transform"
        enter-from-class="opacity-0 translate-y-8 scale-95"
        enter-to-class="opacity-100 translate-y-0 scale-100"
        leave-active-class="transition ease-in duration-200 transform"
        leave-from-class="opacity-100 translate-y-0 scale-100"
        leave-to-class="opacity-0 translate-y-8 scale-95"
    >
        <div
            v-if="showMoreMenu"
            class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4 bg-black/60 backdrop-blur-md md:hidden"
            @click.self="showMoreMenu = false"
        >
            <div class="w-full max-w-sm rounded-3xl p-5 bg-slate-950/95 border border-white/20 shadow-2xl text-white mb-20 relative overflow-hidden">
                <!-- Header Drawer -->
                <div class="flex items-center justify-between border-b border-white/10 pb-3 mb-3">
                    <div>
                        <h4 class="text-sm font-black text-white">Menu Lainnya</h4>
                        <p class="text-[11px] text-zinc-400">Pusat kontrol admin Piramid</p>
                    </div>
                    <button
                        type="button"
                        @click="showMoreMenu = false"
                        class="h-7 w-7 rounded-full bg-white/10 flex items-center justify-center text-zinc-400 hover:text-white"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Navigation List -->
                <div class="space-y-1.5">
                    <Link
                        v-for="sub in secondaryNav"
                        :key="sub.href"
                        :href="sub.href"
                        @click="showMoreMenu = false"
                        class="flex items-center gap-3 p-3 rounded-2xl transition duration-150"
                        :class="isActive(sub.href) ? 'bg-brand-500/20 text-brand-400 border border-brand-500/30 font-bold' : 'hover:bg-white/5 text-zinc-200'"
                    >
                        <div class="h-8 w-8 rounded-xl bg-white/10 flex items-center justify-center text-amber-300">
                            <!-- Gallery Icon -->
                            <svg v-if="sub.icon === 'gallery'" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <!-- Users Icon -->
                            <svg v-else-if="sub.icon === 'users'" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <!-- Public Icon -->
                            <svg v-else class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </div>
                        <span class="text-xs font-semibold">{{ sub.label }}</span>
                    </Link>

                    <div class="pt-2 border-t border-white/10 mt-2">
                        <Link
                            href="/logout"
                            method="post"
                            as="button"
                            class="flex w-full items-center gap-3 p-3 rounded-2xl text-rose-400 hover:bg-rose-500/10 transition text-xs font-bold"
                        >
                            <div class="h-8 w-8 rounded-xl bg-rose-500/10 flex items-center justify-center text-rose-400">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                            </div>
                            <span>Keluar dari Akun Admin</span>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>
