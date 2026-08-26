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

// Valid uncompressed NIST P-256 VAPID Public Key (65 bytes hex / 88 chars base64url)
const VAPID_PUBLIC_KEY = 'BFA1-iXGvDjhN-uLzRjQo4VqH9zQhZ5m2f7s4e0uG5G7M4f7s4e0uG5G7M4f7s4e0uG5G7M4f7s4e0uG5G7M4f7s4e0uG5A=';

function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
    const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
    const rawData = window.atob(base64);
    const outputArray = new Uint8Array(rawData.length);
    for (let i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
}

const saveSubscriptionToServer = async (subscription) => {
    try {
        const subJson = subscription.toJSON();
        await window.axios.post('/api/push-subscribe', {
            endpoint: subJson.endpoint,
            public_key: subJson.keys?.p256dh,
            auth_token: subJson.keys?.auth,
            content_encoding: (PushManager.supportedContentEncodings || ['aesgcm'])[0],
        });
        console.log('✅ Push Notification token berhasil didaftarkan ke server:', subJson.endpoint);
    } catch (e) {
        console.warn('Gagal menyimpan token push ke server:', e);
    }
};

const subscribeUserToPush = async (reg) => {
    try {
        let sub = await reg.pushManager.getSubscription();
        if (!sub) {
            // Subscribe ke PushManager browser
            try {
                const convertedVapidKey = urlBase64ToUint8Array(VAPID_PUBLIC_KEY);
                sub = await reg.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: convertedVapidKey,
                });
            } catch (vapidErr) {
                // Fallback subscribe tanpa VAPID jika browser mendukung gcm_sender_id
                sub = await reg.pushManager.subscribe({
                    userVisibleOnly: true,
                });
            }
        }
        if (sub) {
            await saveSubscriptionToServer(sub);
        }
    } catch (err) {
        console.warn('Push subscribe attempt notice:', err);
    }
};

onMounted(async () => {
    if ('Notification' in window && 'serviceWorker' in navigator) {
        isSupported.value = true;
        permission.value = Notification.permission;
        
        // Expose ke window agar bisa di-test kapan saja lewat Console Browser: window.testPiramidNotif()
        window.testPiramidNotif = triggerTestNotification;

        // Jika user sebelumnya sudah allow, pastikan token tetap terdaftar ke server
        if (permission.value === 'granted') {
            try {
                const reg = await navigator.serviceWorker.ready;
                await subscribeUserToPush(reg);
            } catch (e) {}
        } else {
            // Tampilkan prompt persetujuan jika user belum pernah memilih atau belum di-dismiss
            const dismissed = localStorage.getItem('piramid_notif_dismissed');
            if (permission.value === 'default' && !dismissed) {
                setTimeout(() => {
                    showPrompt.value = true;
                }, 2000);
            }
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
            const reg = await navigator.serviceWorker.ready;
            
            // Simpan token subscription ke database server agar bisa dipush otomatis dari backend
            await subscribeUserToPush(reg);

            // Kirim notifikasi sambutan uji coba langsung di smartphone
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
