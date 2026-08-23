<?php

return [
    'merchant_id' => env('MIDTRANS_MERCHANT_ID', 'G778086072'),
    'server_key' => env('MIDTRANS_SERVER_KEY', 'SB-Mid-server-UWdxhKL11SJqG3c8T0TFyfvo'),
    'client_key' => env('MIDTRANS_CLIENT_KEY', 'SB-Mid-client-yq36YrCJTtq0Cb2A'),
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
    'is_sanitized' => true,
    'is_3ds' => true,
];
