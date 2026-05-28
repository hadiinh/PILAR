<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class KeluargaController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q'));

        $query = User::query()
            ->whereNotNull('no_kk')
            ->selectRaw('
                no_kk,
                COUNT(*) as jumlah_anggota,
                MAX(CASE WHEN is_kepala_keluarga = 1 THEN name END) as nama_kepala,
                MAX(CASE WHEN is_kepala_keluarga = 1 THEN id END) as kepala_id,
                MAX(rt) as rt,
                MAX(rw) as rw,
                MAX(no_rumah) as no_rumah,
                MAX(kelurahan_nama) as kelurahan_nama,
                MAX(kecamatan_nama) as kecamatan_nama
            ')
            ->groupBy('no_kk')
            ->orderBy('no_kk');

        if ($q !== '') {
            $query->where(function ($qq) use ($q) {
                $qq->where('no_kk', 'like', "%{$q}%")
                   ->orWhere('name', 'like', "%{$q}%");
            });
        }

        $items = $query->paginate(20)->withQueryString();

        $stats = [
            'total_kk'      => User::whereNotNull('no_kk')->distinct('no_kk')->count('no_kk'),
            'total_anggota' => User::whereNotNull('no_kk')->count(),
            'kepala'        => User::where('is_kepala_keluarga', true)->count(),
        ];

        return view('keluarga.index', compact('items', 'q', 'stats'));
    }

    public function show(string $noKk)
    {
        $anggota = User::where('no_kk', $noKk)
            ->orderByDesc('is_kepala_keluarga')
            ->orderBy('tanggal_lahir')
            ->get();

        if ($anggota->isEmpty()) {
            abort(404, 'Data keluarga tidak ditemukan');
        }

        $kepala = $anggota->firstWhere('is_kepala_keluarga', true) ?? $anggota->first();

        return view('keluarga.show', compact('anggota', 'kepala', 'noKk'));
    }
}
