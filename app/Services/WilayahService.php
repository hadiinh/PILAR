<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Membungkus API wilayah Indonesia (emsifa).
 * Hasil dicache supaya request berulang cepat.
 *
 * Endpoint:
 *   provinces.json
 *   regencies/{provinceId}.json
 *   districts/{regencyId}.json
 *   villages/{districtId}.json
 */
class WilayahService
{
    public function __construct(
        protected ?string $base = null,
        protected ?int $ttl = null,
        protected ?int $timeout = null,
    ) {
        $this->base    = rtrim($base ?? (string) config('wilayah.base'), '/');
        $this->ttl     = $ttl     ?? (int) config('wilayah.cache_ttl', 86400);
        $this->timeout = $timeout ?? (int) config('wilayah.timeout', 15);
    }

    public function provinsi(): array
    {
        return $this->fetch('provinces.json', 'wilayah:provinces');
    }

    public function kota(string $provinsiId): array
    {
        $provinsiId = preg_replace('/[^0-9]/', '', $provinsiId);
        return $this->fetch("regencies/{$provinsiId}.json", "wilayah:regencies:{$provinsiId}");
    }

    public function kecamatan(string $kotaId): array
    {
        $kotaId = preg_replace('/[^0-9]/', '', $kotaId);
        return $this->fetch("districts/{$kotaId}.json", "wilayah:districts:{$kotaId}");
    }

    public function kelurahan(string $kecamatanId): array
    {
        $kecamatanId = preg_replace('/[^0-9]/', '', $kecamatanId);
        return $this->fetch("villages/{$kecamatanId}.json", "wilayah:villages:{$kecamatanId}");
    }

    protected function fetch(string $path, string $cacheKey): array
    {
        return Cache::remember($cacheKey, $this->ttl, function () use ($path) {
            try {
                $response = Http::timeout($this->timeout)->get("{$this->base}/{$path}");
                if ($response->successful()) {
                    return $response->json() ?? [];
                }
                Log::warning('[Wilayah] Gagal mengambil data', [
                    'path'   => $path,
                    'status' => $response->status(),
                ]);
            } catch (\Throwable $e) {
                Log::error('[Wilayah] Exception saat fetch', [
                    'path'  => $path,
                    'error' => $e->getMessage(),
                ]);
            }
            return [];
        });
    }
}
