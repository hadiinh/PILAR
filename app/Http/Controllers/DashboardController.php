<?php

namespace App\Http\Controllers;

use App\Models\Foto;
use App\Models\Jadwal;
use App\Models\Kegiatan;
use App\Models\Keuangan;
use App\Models\Laporan;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $isManager = in_array($user->role, ['ketua_rw', 'admin']);

        $masuk  = (int) Keuangan::where('tipe', 'masuk')->sum('jumlah');
        $keluar = (int) Keuangan::where('tipe', 'keluar')->sum('jumlah');

        $laporanQuery = $isManager ? Laporan::query() : Laporan::where('user_id', $user->id);

        $stats = [
            'total_user'      => User::count(),
            'total_kegiatan'  => Kegiatan::count(),
            'total_jadwal'    => Jadwal::count(),
            'total_foto'      => Foto::count(),
            'total_masuk'     => $masuk,
            'total_keluar'    => $keluar,
            'saldo_kas'       => $masuk - $keluar,
            'laporan_baru'    => (clone $laporanQuery)->where('status', 'baru')->count(),
            'laporan_diproses'=> (clone $laporanQuery)->where('status', 'diproses')->count(),
            'laporan_selesai' => (clone $laporanQuery)->where('status', 'selesai')->count(),
            'jadwal_mendatang'=> Jadwal::where('tanggal', '>=', now()->toDateString())->count(),
        ];

        $recentJadwal   = Jadwal::orderBy('tanggal', 'desc')->take(5)->get();
        $recentLaporan  = (clone $laporanQuery)->latest()->take(5)->get();
        $recentKeuangan = Keuangan::latest()->take(5)->get();
        $recentFoto     = Foto::latest()->take(6)->get();

        return view('dashboard.index', compact(
            'stats', 'recentJadwal', 'recentLaporan', 'recentKeuangan', 'recentFoto', 'isManager'
        ));
    }
}
