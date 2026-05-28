<?php

namespace App\Http\Controllers;

use App\Models\Foto;
use App\Models\Jadwal;
use App\Models\Kegiatan;
use App\Models\Keuangan;
use App\Models\Laporan;
use App\Models\User;
use Carbon\Carbon;

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

        // Data grafik keuangan 12 bulan terakhir (pemasukan, pengeluaran, saldo bulanan)
        $chart = $this->buildChartKeuangan(12);

        return view('dashboard.index', compact(
            'stats', 'recentJadwal', 'recentLaporan', 'recentKeuangan',
            'recentFoto', 'isManager', 'chart'
        ));
    }

    /**
     * Bangun dataset 12 bulan ke belakang untuk Chart.js.
     */
    protected function buildChartKeuangan(int $bulan = 12): array
    {
        $labels   = [];
        $pemasukan = [];
        $pengeluaran = [];
        $saldoRunning = [];

        // hitung saldo awal sebelum periode
        $start = Carbon::now()->startOfMonth()->subMonths($bulan - 1);
        $saldoSebelum = (int) Keuangan::where('tipe', 'masuk')
                ->where('tanggal', '<', $start->toDateString())
                ->sum('jumlah')
            - (int) Keuangan::where('tipe', 'keluar')
                ->where('tanggal', '<', $start->toDateString())
                ->sum('jumlah');

        $saldo = $saldoSebelum;

        for ($i = 0; $i < $bulan; $i++) {
            $cursor = (clone $start)->addMonths($i);
            $awal   = $cursor->copy()->startOfMonth()->toDateString();
            $akhir  = $cursor->copy()->endOfMonth()->toDateString();

            $m = (int) Keuangan::where('tipe', 'masuk')
                ->whereBetween('tanggal', [$awal, $akhir])
                ->sum('jumlah');
            $k = (int) Keuangan::where('tipe', 'keluar')
                ->whereBetween('tanggal', [$awal, $akhir])
                ->sum('jumlah');

            $saldo += ($m - $k);

            $labels[]      = $cursor->translatedFormat('M Y');
            $pemasukan[]   = $m;
            $pengeluaran[] = $k;
            $saldoRunning[] = $saldo;
        }

        return [
            'labels'      => $labels,
            'pemasukan'   => $pemasukan,
            'pengeluaran' => $pengeluaran,
            'saldo'       => $saldoRunning,
        ];
    }
}
