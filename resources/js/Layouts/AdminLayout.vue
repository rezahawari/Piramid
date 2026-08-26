<script setup>
import BrandLogo from '@/Components/BrandLogo.vue';
import ToastNotification from '@/Components/ToastNotification.vue';
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const showingSidebar = ref(false);

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
    <div class="min-h-screen bg-gray-100">
        <ToastNotification />
        <nav class="border-b border-gray-100 bg-white">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <div class="flex items-center gap-6">
                        <Link href="/admin" class="flex items-center gap-2">
                            <BrandLogo />
                            <span class="font-semibold text-gray-800">Admin Piramid</span>
                        </Link>
                        <div class="hidden gap-4 sm:flex">
                            <Link
                                v-for="item in navItems"
                                :key="item.href"
                                :href="item.href"
                                class="inline-flex items-center border-b-2 px-1 pt-1 text-sm font-medium"
                                :class="
                                    $page.url.startsWith(item.href) &&
                                    (item.href !== '/admin' || $page.url === '/admin')
                                        ? 'border-brand-400 text-gray-900'
                                        : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'
                                "
                            >
                                {{ item.label }}
                            </Link>
                        </div>
                    </div>
                    <div class="hidden items-center gap-4 sm:flex">
                        <span class="text-sm text-gray-600">{{ $page.props.auth.user.name }}</span>
                        <Link
                            href="/logout"
                            method="post"
                            as="button"
                            class="text-sm text-gray-500 hover:text-gray-700"
                        >
                            Keluar
                        </Link>
                    </div>
                    <button
                        class="sm:hidden"
                        @click="showingSidebar = !showingSidebar"
                    >
                        <svg class="h-6 w-6 text-gray-500" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
            <div v-show="showingSidebar" class="space-y-1 pb-3 pt-2 sm:hidden">
                <Link
                    v-for="item in navItems"
                    :key="item.href"
                    :href="item.href"
                    class="block py-2 pl-4 pr-4 text-base font-medium text-gray-600 hover:bg-gray-50"
                >
                    {{ item.label }}
                </Link>
                <Link
                    href="/logout"
                    method="post"
                    as="button"
                    class="block w-full py-2 pl-4 pr-4 text-left text-base font-medium text-gray-600 hover:bg-gray-50"
                >
                    Keluar
                </Link>
            </div>
        </nav>

        <header v-if="$slots.header" class="bg-white shadow">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <slot name="header" />
            </div>
        </header>

        <main>
            <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                <slot />
            </div>
        </main>
    </div>
</template>
