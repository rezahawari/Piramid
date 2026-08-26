<script setup>
import BrandLogo from '@/Components/BrandLogo.vue';
import MobileBottomNav from '@/Components/MobileBottomNav.vue';
import PushNotificationPrompt from '@/Components/PushNotificationPrompt.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth?.user);

const isActive = (href) =>
    href === '/' ? page.url === '/' : page.url.startsWith(href);
</script>

<template>
    <!-- Push Notification Modal Prompt for PWA Mobile -->
    <PushNotificationPrompt />

    <!-- Desktop Header Navbar (Disembunyikan di Mobile) -->
    <nav class="sticky top-0 z-40 border-b border-zinc-100 bg-white/95 backdrop-blur hidden md:block">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-8">
                <Link href="/" aria-label="Beranda Piramid">
                    <BrandLogo />
                </Link>
                <div class="flex items-center gap-6 text-sm font-semibold">
                    <Link
                        href="/"
                        :class="isActive('/') ? 'text-brand-500' : 'text-zinc-900 hover:text-brand-500'"
                    >
                        Beranda
                    </Link>
                    <Link
                        href="/layanan"
                        :class="isActive('/layanan') ? 'text-brand-500' : 'text-zinc-900 hover:text-brand-500'"
                    >
                        Pemesanan
                    </Link>
                    <Link
                        v-if="user"
                        href="/transaksi"
                        :class="isActive('/transaksi') ? 'text-brand-500' : 'text-zinc-900 hover:text-brand-500'"
                    >
                        Transaksi
                    </Link>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <template v-if="user">
                    <span class="text-sm font-semibold text-zinc-700">{{ user.name }}</span>
                    <Link
                        v-if="user.role === 'admin'"
                        href="/admin"
                        class="rounded-md border border-zinc-200 px-4 py-2 text-sm font-semibold text-zinc-900 hover:border-brand-500 hover:text-brand-500"
                    >
                        Admin
                    </Link>
                    <Link
                        href="/logout"
                        method="post"
                        as="button"
                        class="rounded-md bg-brand-500 px-4 py-2 text-sm font-semibold text-brand-50 hover:bg-brand-600"
                    >
                        Keluar
                    </Link>
                </template>
                <template v-else>
                    <Link
                        :href="route('login')"
                        class="rounded-md border border-zinc-200 px-4 py-2 text-sm font-semibold text-zinc-900 hover:border-brand-500 hover:text-brand-500"
                    >
                        Sign In
                    </Link>
                    <Link
                        :href="route('register')"
                        class="rounded-md bg-brand-500 px-4 py-2 text-sm font-semibold text-brand-50 hover:bg-brand-600"
                    >
                        Sign Up
                    </Link>
                </template>
            </div>
        </div>
    </nav>

    <!-- Mobile Floating Liquid Glass Snackbar / Nav -->
    <MobileBottomNav />
</template>


