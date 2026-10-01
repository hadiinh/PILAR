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

    // Aktifkan/nonaktifkan pengiriman WA (mis. untuk env testing).
    // Wajib filter_var: env() mengembalikan string, "(bool) 'false'" bernilai true.
    'enabled'  => filter_var(env('FONNTE_ENABLED', true), FILTER_VALIDATE_BOOL),

    // Default country code (Indonesia)
    'country_code' => env('FONNTE_COUNTRY_CODE', '62'),

    // Timeout request HTTP (detik)
    'timeout'  => (int) env('FONNTE_TIMEOUT', 15),

    // Kirim broadcast via queue (perlu worker berjalan: php artisan queue:work).
    // Bila false, broadcast dikirim langsung/sinkron seperti notifikasi
    // pengajuan akun — dipakai saat jumlah warga sedikit & tanpa worker queue.
    'queue'    => filter_var(env('FONNTE_QUEUE', false), FILTER_VALIDATE_BOOL),
];
