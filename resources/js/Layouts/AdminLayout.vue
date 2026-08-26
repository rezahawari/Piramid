<script setup>
import BrandLogo from '@/Components/BrandLogo.vue';
import MobileBottomAdminNav from '@/Components/MobileBottomAdminNav.vue';
import ToastNotification from '@/Components/ToastNotification.vue';
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const navItems = [
    { label: 'Dashboard', href: '/admin' },
    { label: 'Layanan', href: '/admin/layanan' },
    { label: 'Produk Hewan', href: '/admin/produk' },
    { label: 'Galeri & Edukasi', href: '/admin/galeri' },
    { label: 'Transaksi', href: '/admin/transaksi' },
    { label: 'Pengguna', href: '/admin/users' },
];
</script>

<template>
    <div class="min-h-screen bg-slate-100 font-sans pb-28 md:pb-0">
        <ToastNotification />
        
        <!-- Mobile Bottom Liquid Glass Snackbar -->
        <MobileBottomAdminNav />

        <!-- Desktop Top Navbar (Disembunyikan di Mobile) -->
        <nav class="border-b border-gray-200/80 bg-white/95 backdrop-blur hidden md:block sticky top-0 z-40">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <div class="flex items-center gap-6">
                        <Link href="/admin" class="flex items-center gap-2">
                            <BrandLogo />
                            <span class="font-bold text-gray-900 tracking-tight">Admin Piramid</span>
                        </Link>
                        <div class="flex gap-1">
                            <Link
                                v-for="item in navItems"
                                :key="item.href"
                                :href="item.href"
                                class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-bold transition duration-150"
                                :class="
                                    $page.url.startsWith(item.href) &&
                                    (item.href !== '/admin' || $page.url === '/admin')
                                        ? 'bg-brand-50 text-brand-700'
                                        : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'
                                "
                            >
                                {{ item.label }}
                            </Link>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="text-xs font-semibold text-gray-700 bg-gray-100 px-3 py-1.5 rounded-full">
                            👤 {{ $page.props.auth.user.name }}
                        </span>
                        <Link
                            href="/logout"
                            method="post"
                            as="button"
                            class="text-xs font-bold text-rose-600 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 px-3 py-1.5 rounded-xl transition"
                        >
                            Keluar
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Desktop Heading Header -->
        <header v-if="$slots.header" class="bg-white shadow-xs hidden md:block border-b border-gray-100">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <slot name="header" />
            </div>
        </header>

        <main>
            <div class="mx-auto max-w-7xl px-3 py-4 sm:px-6 sm:py-8 lg:px-8">
                <slot />
            </div>
        </main>
    </div>
</template>
