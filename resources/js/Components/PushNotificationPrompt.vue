<script setup>
import { onMounted, ref } from 'vue';

const isSupported = ref(false);
const permission = ref('default');
const isSubscribed = ref(false);
const showPrompt = ref(false);
const isProcessing = ref(false);

const triggerTestNotification = async (title = '🔔 Uji Coba Status Qurban', message = 'Hewan qurban Anda telah selesai disembelih & video dokumentasi siap diunduh!') => {
    if (!('serviceWorker' in navigator)) {
        alert('Browser tidak mendukung Service Worker.');
        return;
    }

    const reg = await navigator.serviceWorker.ready;
    if (Notification.permission === 'granted') {
        reg.showNotification(title, {
            body: message,
            icon: '/images/icon-192x192.png',
            badge: '/images/icon-72x72.png',
            vibrate: [100, 50, 100, 50, 150],
            data: {
                url: '/transaksi',
                dateOfArrival: Date.now(),
            },
        });
    } else {
        requestPermission();
    }
};

onMounted(() => {
    if ('Notification' in window && 'serviceWorker' in navigator) {
        isSupported.value = true;
        permission.value = Notification.permission;
        
        // Expose ke window agar bisa di-test kapan saja lewat Console Browser: window.testPiramidNotif()
        window.testPiramidNotif = triggerTestNotification;

        // Tampilkan prompt persetujuan jika user belum pernah memilih atau belum di-dismiss
        const dismissed = localStorage.getItem('piramid_notif_dismissed');
        if (permission.value === 'default' && !dismissed) {
            setTimeout(() => {
                showPrompt.value = true;
            }, 2500); // Muncul setelah 2.5 detik agar tidak mengagetkan user
        }
    }
});

const requestPermission = async () => {
    if (!isSupported.value) return;
    isProcessing.value = true;

    try {
        const result = await Notification.requestPermission();
        permission.value = result;

        if (result === 'granted') {
            showPrompt.value = false;
            // Kirim notifikasi sambutan uji coba langsung di smartphone
            const reg = await navigator.serviceWorker.ready;
            reg.showNotification('🎉 Notifikasi Piramid Aktif!', {
                body: 'Anda akan menerima pembaruan status pesanan qurban & dokumentasi tepat waktu.',
                icon: '/images/icon-192x192.png',
                badge: '/images/icon-72x72.png',
                vibrate: [100, 50, 100],
            });
        } else {
            showPrompt.value = false;
            localStorage.setItem('piramid_notif_dismissed', 'true');
        }
    } catch (err) {
        console.error('Notification error:', err);
    } finally {
        isProcessing.value = false;
    }
};

const dismiss = () => {
    showPrompt.value = false;
    localStorage.setItem('piramid_notif_dismissed', 'true');
};
</script>

<template>
    <!-- Liquid Glass Native App Notification Banner Prompt -->
    <transition
        enter-active-class="transition ease-out duration-300 transform"
        enter-from-class="opacity-0 translate-y-8 scale-95"
        enter-to-class="opacity-100 translate-y-0 scale-100"
        leave-active-class="transition ease-in duration-200 transform"
        leave-from-class="opacity-100 translate-y-0 scale-100"
        leave-to-class="opacity-0 translate-y-8 scale-95"
    >
        <div
            v-if="showPrompt && isSupported && permission === 'default'"
            class="fixed top-4 inset-x-0 z-50 flex justify-center px-4 pointer-events-none"
        >
            <div class="pointer-events-auto w-full max-w-md rounded-3xl p-4 bg-slate-950/90 backdrop-blur-2xl border border-white/20 shadow-2xl text-white relative overflow-hidden">
                <!-- Ambient Glow -->
                <div class="absolute -top-10 -right-10 w-28 h-28 bg-amber-400/20 rounded-full blur-2xl pointer-events-none"></div>

                <div class="flex items-start gap-3.5 relative z-10">
                    <div class="h-10 w-10 rounded-2xl bg-gradient-to-tr from-brand-600 to-amber-400 p-0.5 shadow-md flex items-center justify-center shrink-0">
                        <div class="h-full w-full rounded-2xl bg-slate-900 flex items-center justify-center text-amber-300">
                            <svg class="h-5 w-5 animate-bounce" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </div>
                    </div>

                    <div class="flex-1">
                        <h4 class="text-sm font-black text-white tracking-tight flex items-center gap-1.5">
                            Aktifkan Notifikasi Pesanan
                            <span class="flex h-2 w-2 rounded-full bg-emerald-400"></span>
                        </h4>
                        <p class="text-xs text-zinc-300 mt-1 leading-relaxed">
                            Dapatkan pemberitahuan langsung di HP saat bukti bayar diverifikasi, hewan disembelih, dan video dokumentasi siap diunduh.
                        </p>

                        <div class="mt-3.5 flex items-center gap-2">
                            <button
                                type="button"
                                @click="requestPermission"
                                :disabled="isProcessing"
                                class="flex-1 rounded-xl bg-gradient-to-r from-brand-600 to-brand-700 hover:from-brand-700 hover:to-brand-800 text-white font-bold py-2 px-3 text-xs shadow-md shadow-brand-600/30 transition active:scale-95 cursor-pointer text-center"
                            >
                                {{ isProcessing ? 'Memproses...' : 'Izinkan Notifikasi' }}
                            </button>
                            <button
                                type="button"
                                @click="dismiss"
                                class="rounded-xl bg-white/10 hover:bg-white/20 border border-white/15 px-3 py-2 text-xs font-semibold text-zinc-300 transition active:scale-95 cursor-pointer"
                            >
                                Nanti Saja
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>
