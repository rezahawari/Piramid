<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetAppLocale
{
    /**
     * Handle an incoming request and set application locale based on Accept-Language header.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->header('Accept-Language', 'id');

        // Normalisasi format seperti 'en-US,en;q=0.9' atau 'id-ID' -> ambil 2 huruf awal
        if (str_contains($locale, ',')) {
            $locale = explode(',', $locale)[0];
        }
        if (str_contains($locale, '-')) {
            $locale = explode('-', $locale)[0];
        }

        if (in_array($locale, ['id', 'en', 'zh', 'ar'])) {
            App::setLocale($locale);
        } else {
            App::setLocale('id');
        }

        return $next($request);
    }
}
