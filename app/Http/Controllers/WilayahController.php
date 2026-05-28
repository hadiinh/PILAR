<?php

namespace App\Http\Controllers;

use App\Services\WilayahService;
use Illuminate\Http\JsonResponse;

class WilayahController extends Controller
{
    public function __construct(protected WilayahService $wilayah) {}

    public function provinsi(): JsonResponse
    {
        return response()->json($this->wilayah->provinsi());
    }

    public function kota(string $provinsiId): JsonResponse
    {
        return response()->json($this->wilayah->kota($provinsiId));
    }

    public function kecamatan(string $kotaId): JsonResponse
    {
        return response()->json($this->wilayah->kecamatan($kotaId));
    }

    public function kelurahan(string $kecamatanId): JsonResponse
    {
        return response()->json($this->wilayah->kelurahan($kecamatanId));
    }
}
