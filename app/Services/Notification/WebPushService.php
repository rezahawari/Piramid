<?php

namespace App\Services\Notification;

use App\Models\PushSubscription;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class WebPushService
{
    /**
     * Kirim push notification ke user tertentu
     */
    public function sendToUser(User $user, string $title, string $body, string $url = '/transaksi'): void
    {
        $subscriptions = $user->pushSubscriptions()->get();
        if ($subscriptions->isEmpty()) {
            return;
        }

        $this->dispatchPush($subscriptions, $title, $body, $url);
    }

    /**
     * Kirim push notification ke semua pengguna (Broadcast)
     */
    public function broadcast(string $title, string $body, string $url = '/layanan'): void
    {
        $subscriptions = PushSubscription::all();
        if ($subscriptions->isEmpty()) {
            return;
        }

        $this->dispatchPush($subscriptions, $title, $body, $url);
    }

    /**
     * Dispatch notification payload via WebPush atau HTTP Push
     */
    protected function dispatchPush($subscriptions, string $title, string $body, string $url): void
    {
        $payload = json_encode([
            'title' => $title,
            'body' => $body,
            'url' => $url,
            'icon' => '/images/icon-192x192.png',
            'badge' => '/images/icon-72x72.png',
        ]);

        $vapidSubject = env('VAPID_SUBJECT', env('APP_URL', 'https://app.piramidqurban.com'));
        $vapidPublicKey = env('VAPID_PUBLIC_KEY', 'BLZ49N4zM3bO5wW11wUqyX7E9qK4h11w3UqyX7E9qK4h11w3UqyX7E9qK4h11w3UqyX7E9qK4h11w3UqyX7E9qK4=');
        $vapidPrivateKey = env('VAPID_PRIVATE_KEY', 'x_UqyX7E9qK4h11w3UqyX7E9qK4h11w3UqyX7E9qK4=');

        // Jika class WebPush dari minishlink tersedia
        if (class_exists(WebPush::class)) {
            try {
                $auth = [
                    'VAPID' => [
                        'subject' => $vapidSubject,
                        'publicKey' => $vapidPublicKey,
                        'privateKey' => $vapidPrivateKey,
                    ],
                ];

                $webPush = new WebPush($auth);

                foreach ($subscriptions as $sub) {
                    $subscription = Subscription::create([
                        'endpoint' => $sub->endpoint,
                        'publicKey' => $sub->public_key,
                        'authToken' => $sub->auth_token,
                        'contentEncoding' => $sub->content_encoding ?? 'aesgcm',
                    ]);

                    $webPush->queueNotification($subscription, $payload);
                }

                foreach ($webPush->flush() as $report) {
                    $endpoint = $report->getRequest()->getUri()->__toString();
                    if ($report->isSubscriptionExpired()) {
                        PushSubscription::where('endpoint', $endpoint)->delete();
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('WebPush minishlink send notice: ' . $e->getMessage());
            }
        } else {
            // Native HTTP Fallback for direct push endpoints
            foreach ($subscriptions as $sub) {
                try {
                    Http::timeout(3)->post($sub->endpoint, [
                        'payload' => $payload,
                    ]);
                } catch (\Throwable) {
                    // Ignore expired client
                }
            }
        }
    }
}
