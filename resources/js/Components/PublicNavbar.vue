<script setup>
import BrandLogo from '@/Components/BrandLogo.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth?.user);
const open = ref(false);

const isActive = (href) =>
    href === '/' ? page.url === '/' : page.url.startsWith(href);
</script>

<template>
    <nav class="sticky top-0 z-40 border-b border-zinc-100 bg-white/95 backdrop-blur">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-8">
                <Link href="/" aria-label="Beranda Piramid">
                    <BrandLogo />
                </Link>
                <div class="hidden items-center gap-6 text-sm font-semibold sm:flex">
                    <Link
                        href="/"
                        :class="isActive('/') ? 'text-brand-500' : 'text-zinc-900 hover:text-brand-500'"
                    >
                        Beranda
                    </Link>
                    <Link
                        href="/layanan/qurban"
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

            <div class="hidden items-center gap-3 sm:flex">
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

            <button class="sm:hidden" aria-label="Menu" @click="open = !open">
                <svg class="h-6 w-6 text-zinc-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>

        <div v-show="open" class="space-y-1 border-t border-zinc-100 px-4 pb-4 pt-2 sm:hidden">
            <Link href="/" class="block py-2 font-semibold text-zinc-900">Beranda</Link>
            <Link href="/layanan/qurban" class="block py-2 font-semibold text-zinc-900">Pemesanan</Link>
            <Link v-if="user" href="/transaksi" class="block py-2 font-semibold text-zinc-900">Transaksi</Link>
            <template v-if="user">
                <Link v-if="user.role === 'admin'" href="/admin" class="block py-2 font-semibold text-zinc-900">Admin</Link>
                <Link href="/logout" method="post" as="button" class="block py-2 font-semibold text-brand-500">Keluar</Link>
            </template>
            <template v-else>
                <Link :href="route('login')" class="block py-2 font-semibold text-zinc-900">Sign In</Link>
                <Link :href="route('register')" class="block py-2 font-semibold text-brand-500">Sign Up</Link>
            </template>
        </div>
    </nav>
</template>
