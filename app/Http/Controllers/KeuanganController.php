<?php

namespace App\Http\Controllers;

use App\Models\Keuangan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class KeuanganController extends Controller
{
    public function index()
    {
        $data = Keuangan::orderBy('tanggal', 'desc')->orderBy('id', 'desc')->get();

        $totalMasuk  = (int) Keuangan::where('tipe', 'masuk')->sum('jumlah');
        $totalKeluar = (int) Keuangan::where('tipe', 'keluar')->sum('jumlah');
        $saldo       = $totalMasuk - $totalKeluar;

        // Statistik bulan ini
        $awalBulan = Carbon::now()->startOfMonth();
        $masukBulanIni = (int) Keuangan::where('tipe', 'masuk')
            ->where('tanggal', '>=', $awalBulan)
            ->sum('jumlah');
        $keluarBulanIni = (int) Keuangan::where('tipe', 'keluar')
            ->where('tanggal', '>=', $awalBulan)
            ->sum('jumlah');

        // Data chart 12 bulan
        $chart = $this->buildChart(12);

        return view('keuangan.index', compact(
            'data', 'saldo', 'totalMasuk', 'totalKeluar',
            'masukBulanIni', 'keluarBulanIni', 'chart'
        ));
    }

    public function create()
    {
        return view('keuangan.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul'     => 'required|string|max:255',
            'tipe'      => 'required|in:masuk,keluar',
            'jumlah'    => 'required|numeric|min:0',
            'tanggal'   => 'required|date',
            'deskripsi' => 'nullable|string|max:1000',
        ]);

        Keuangan::create($validated);

        return redirect('/keuangan')->with('success', 'Transaksi berhasil ditambahkan.');
    }

    public function edit(Keuangan $keuangan)
    {
        return view('keuangan.edit', compact('keuangan'));
    }

    public function update(Request $request, Keuangan $keuangan)
    {
        $validated = $request->validate([
            'judul'     => 'required|string|max:255',
            'tipe'      => 'required|in:masuk,keluar',
            'jumlah'    => 'required|numeric|min:0',
            'tanggal'   => 'required|date',
            'deskripsi' => 'nullable|string|max:1000',
        ]);

        $keuangan->update($validated);

        return redirect('/keuangan')->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function destroy(Keuangan $keuangan)
    {
        $keuangan->delete();
        return redirect('/keuangan')->with('success', 'Transaksi berhasil dihapus.');
    }

    /**
     * Bangun chart 12 bulan: pemasukan, pengeluaran, saldo running.
     */
    protected function buildChart(int $bulan = 12): array
    {
        $labels = [];
        $pemasukan = [];
        $pengeluaran = [];
        $saldoRunning = [];

        $start = Carbon::now()->startOfMonth()->subMonths($bulan - 1);

        $saldoSebelum = (int) Keuangan::where('tipe', 'masuk')
                ->where('tanggal', '<', $start->toDateString())->sum('jumlah')
            - (int) Keuangan::where('tipe', 'keluar')
                ->where('tanggal', '<', $start->toDateString())->sum('jumlah');

        $saldo = $saldoSebelum;

        for ($i = 0; $i < $bulan; $i++) {
            $cursor = (clone $start)->addMonths($i);
            $awal   = $cursor->copy()->startOfMonth()->toDateString();
            $akhir  = $cursor->copy()->endOfMonth()->toDateString();

            $m = (int) Keuangan::where('tipe', 'masuk')->whereBetween('tanggal', [$awal, $akhir])->sum('jumlah');
            $k = (int) Keuangan::where('tipe', 'keluar')->whereBetween('tanggal', [$awal, $akhir])->sum('jumlah');

            $saldo += ($m - $k);

            $labels[] = $cursor->translatedFormat('M Y');
            $pemasukan[] = $m;
            $pengeluaran[] = $k;
            $saldoRunning[] = $saldo;
        }

        return compact('labels', 'pemasukan', 'pengeluaran', 'saldoRunning');
    }
}
