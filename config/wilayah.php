<?php

return [
    'base'      => env('WILAYAH_API_BASE', 'https://emsifa.github.io/api-wilayah-indonesia/api'),
    'cache_ttl' => (int) env('WILAYAH_CACHE_TTL', 86400),
    'timeout'   => (int) env('WILAYAH_TIMEOUT', 15),
];
