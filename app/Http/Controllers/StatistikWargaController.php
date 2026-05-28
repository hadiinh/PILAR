<?php

namespace App\Http\Controllers;

use App\Models\PengajuanAkun;
use App\Models\User;
use Carbon\Carbon;

class StatistikWargaController extends Controller
{
    public function index()
    {
        $warga = User::where('role', 'user')->get();

        // Kategori umur
        $byKategori = [
            'anak'   => 0, 'remaja' => 0, 'dewasa' => 0, 'lansia' => 0,
        ];
        foreach ($warga as $w) {
            $k = $w->kategori_umur;
            if ($k && isset($byKategori[$k])) $byKategori[$k]++;
        }

        // Gender
        $byGender = [
            'L' => $warga->where('jenis_kelamin', 'L')->count(),
            'P' => $warga->where('jenis_kelamin', 'P')->count(),
        ];

        // Akun
        $totalAkunAktif    = User::where('akun_aktif', true)->count();
        $totalAkunNonaktif = User::where('akun_aktif', false)->count();
        $totalPending      = PengajuanAkun::where('status', 'pending')->count();

        // Warga per RT
        $byRt = User::query()
            ->where('role', 'user')
            ->whereNotNull('rt')
            ->selectRaw('rt, COUNT(*) as jumlah')
            ->groupBy('rt')
            ->orderBy('rt')
            ->get();

        $rtLabels = $byRt->map(fn ($r) => 'RT ' . str_pad($r->rt, 2, '0', STR_PAD_LEFT))->values()->all();
        $rtData   = $byRt->pluck('jumlah')->all();

        // Pertumbuhan akun 12 bulan terakhir
        $growthLabels = [];
        $growthData   = [];
        $cursor       = Carbon::now()->startOfMonth()->subMonths(11);
        for ($i = 0; $i < 12; $i++) {
            $awal  = (clone $cursor)->addMonths($i)->startOfMonth();
            $akhir = (clone $awal)->endOfMonth();
            $growthLabels[] = $awal->translatedFormat('M Y');
            $growthData[]   = User::whereBetween('created_at', [$awal, $akhir])->count();
        }

        $stats = [
            'total_warga'    => $warga->count(),
            'total_kk'       => User::whereNotNull('no_kk')->distinct('no_kk')->count('no_kk'),
            'total_laki'     => $byGender['L'],
            'total_perempuan'=> $byGender['P'],
            'total_anak'     => $byKategori['anak'],
            'total_remaja'   => $byKategori['remaja'],
            'total_dewasa'   => $byKategori['dewasa'],
            'total_lansia'   => $byKategori['lansia'],
            'akun_aktif'     => $totalAkunAktif,
            'akun_nonaktif'  => $totalAkunNonaktif,
            'akun_pending'   => $totalPending,
        ];

        $chart = [
            'kategori' => [
                'labels' => ['Anak', 'Remaja', 'Dewasa', 'Lansia'],
                'data'   => array_values($byKategori),
            ],
            'gender' => [
                'labels' => ['Laki-laki', 'Perempuan'],
                'data'   => array_values($byGender),
            ],
            'akun' => [
                'labels' => ['Aktif', 'Nonaktif', 'Pending'],
                'data'   => [$totalAkunAktif, $totalAkunNonaktif, $totalPending],
            ],
            'rt' => [
                'labels' => $rtLabels,
                'data'   => $rtData,
            ],
            'growth' => [
                'labels' => $growthLabels,
                'data'   => $growthData,
            ],
        ];

        return view('statistik.index', compact('stats', 'chart'));
    }
}
