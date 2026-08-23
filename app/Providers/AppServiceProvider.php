<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        if (config('app.env') === 'production' || str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        // Global Midtrans Config Initialization
        \Midtrans\Config::$serverKey = (string) (config('midtrans.server_key') ?: env('MIDTRANS_SERVER_KEY', 'SB-Mid-server-UWdxhKL11SJqG3c8T0TFyfvo'));
        \Midtrans\Config::$clientKey = (string) (config('midtrans.client_key') ?: env('MIDTRANS_CLIENT_KEY', 'SB-Mid-client-yq36YrCJTtq0Cb2A'));
        \Midtrans\Config::$isProduction = (bool) config('midtrans.is_production', false);
        \Midtrans\Config::$isSanitized = (bool) config('midtrans.is_sanitized', true);
        \Midtrans\Config::$is3ds = (bool) config('midtrans.is_3ds', true);
    }
}

