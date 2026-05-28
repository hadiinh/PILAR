<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Fonnte WhatsApp Gateway
    |--------------------------------------------------------------------------
    |
    | Konfigurasi integrasi WhatsApp menggunakan layanan Fonnte
    | (https://fonnte.com). Token didapat dari dashboard Fonnte
    | setelah perangkat terhubung.
    |
    */

    'token'    => env('FONNTE_TOKEN', ''),
    'endpoint' => env('FONNTE_ENDPOINT', 'https://api.fonnte.com/send'),

    // Aktifkan/nonaktifkan pengiriman WA (mis. untuk env testing)
    'enabled'  => env('FONNTE_ENABLED', true),

    // Default country code (Indonesia)
    'country_code' => env('FONNTE_COUNTRY_CODE', '62'),

    // Timeout request HTTP (detik)
    'timeout'  => (int) env('FONNTE_TIMEOUT', 15),
];
