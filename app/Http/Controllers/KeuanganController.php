<?php

namespace App\Http\Controllers;

use App\Models\Keuangan;
use App\Models\User;
use App\Services\FonnteService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class KeuanganController extends Controller
{
    private const MIN_TAHUN = 2026;

    public function index()
    {
        $data = Keuangan::orderBy('tanggal', 'desc')->orderBy('id', 'desc')->get();

        // Saldo keseluruhan: semua pemasukan dikurangi semua pengeluaran (tanpa batas waktu)
        $saldo = (int) Keuangan::where('tipe', 'masuk')->sum('jumlah')
            - (int) Keuangan::where('tipe', 'keluar')->sum('jumlah');

        // Statistik bulan berjalan (bulan & tahun nyata saat ini)
        $awalBulan = Carbon::now()->startOfMonth();
        $masukBulanIni = (int) Keuangan::where('tipe', 'masuk')
            ->where('tanggal', '>=', $awalBulan)
            ->sum('jumlah');
        $keluarBulanIni = (int) Keuangan::where('tipe', 'keluar')
            ->where('tanggal', '>=', $awalBulan)
            ->sum('jumlah');

        // Data chart default: Januari–Desember tahun berjalan
        $tahunList = range(self::MIN_TAHUN, now()->year + 5);
        $tahunAktif = now()->year;
        $chart = $this->buildChartForYear($tahunAktif);
        $statsTahun = [
            'masuk'  => (int) Keuangan::where('tipe', 'masuk')->whereYear('tanggal', $tahunAktif)->sum('jumlah'),
            'keluar' => (int) Keuangan::where('tipe', 'keluar')->whereYear('tanggal', $tahunAktif)->sum('jumlah'),
        ];

        return view('keuangan.index', compact(
            'data', 'saldo', 'masukBulanIni', 'keluarBulanIni',
            'chart', 'tahunList', 'tahunAktif', 'statsTahun'
        ));
    }

    public function chartData(Request $request)
    {
        $mode  = $request->string('mode', 'bulanan')->toString();
        $tahun = $request->integer('tahun', now()->year);
        $tahun = max(self::MIN_TAHUN, min($tahun, now()->year + 5));

        $chart = ($mode === 'tahunan')
            ? $this->buildChartTahunan()
            : $this->buildChartForYear($tahun);

        $stats = [
            'masuk'  => (int) Keuangan::where('tipe', 'masuk')->whereYear('tanggal', $tahun)->sum('jumlah'),
            'keluar' => (int) Keuangan::where('tipe', 'keluar')->whereYear('tanggal', $tahun)->sum('jumlah'),
        ];

        return response()->json(array_merge($chart, ['stats' => $stats]));
    }

    public function create()
    {
        return view('keuangan.create');
    }

    public function store(Request $request)
    {
        if ($request->has('jumlah')) {
            $request->merge(['jumlah' => preg_replace('/\D/', '', (string) $request->input('jumlah'))]);
        }

        $validated = $request->validate([
            'judul'     => 'required|string|max:255',
            'tipe'      => 'required|in:masuk,keluar',
            'jumlah'    => 'required|numeric|min:0',
            'tanggal'   => 'required|date',
            'deskripsi' => 'nullable|string|max:1000',
        ]);

        $keuangan = Keuangan::create($validated);

        // Kirim notifikasi WhatsApp ke semua warga aktif yang bisa terima notifikasi
        $fonnte = app(FonnteService::class);
        $waraAktif = User::where('akun_aktif', true)
            ->where('role', 'user')
            ->where('notif_wa_aktif', true)
            ->whereNotNull('no_hp')
            ->get();

        if ($waraAktif->isNotEmpty()) {
            $nominal = 'Rp ' . number_format($validated['jumlah'], 0, ',', '.');
            $pesan = "Info Sistem RW: Telah ditambahkan data keuangan baru oleh Admin/RW.\n"
                . "Keterangan: {$validated['judul']}\n"
                . "Nominal: {$nominal}\n"
                . "Tanggal: " . Carbon::parse($validated['tanggal'])->translatedFormat('d F Y') . "\n"
                . "Silakan cek aplikasi untuk detail lengkap.";

            $fonnte->kirimKeUsers($waraAktif, $pesan, 'keuangan_baru');
        }

        return redirect('/keuangan')->with('success', 'Transaksi berhasil ditambahkan.');
    }

    public function edit(Keuangan $keuangan)
    {
        return view('keuangan.edit', compact('keuangan'));
    }

    public function update(Request $request, Keuangan $keuangan)
    {
        if ($request->has('jumlah')) {
            $request->merge(['jumlah' => preg_replace('/\D/', '', (string) $request->input('jumlah'))]);
        }

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
     * Bangun chart 12 bulan (Januari–Desember) untuk tahun tertentu:
     * pemasukan, pengeluaran, saldo running.
     */
    protected function buildChartForYear(int $tahun): array
    {
        $labels = [];
        $pemasukan = [];
        $pengeluaran = [];
        $saldoRunning = [];

        $start = Carbon::create($tahun, 1, 1);

        $saldoSebelum = (int) Keuangan::where('tipe', 'masuk')
                ->where('tanggal', '<', $start->toDateString())->sum('jumlah')
            - (int) Keuangan::where('tipe', 'keluar')
                ->where('tanggal', '<', $start->toDateString())->sum('jumlah');

        $saldo = $saldoSebelum;

        for ($i = 0; $i < 12; $i++) {
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

    /**
     * Bangun chart agregat: 1 batang per tahun sesuai rentang dropdown
     * (MIN_TAHUN hingga tahun berjalan + 5).
     */
    protected function buildChartTahunan(): array
    {
        $labels = [];
        $pemasukan = [];
        $pengeluaran = [];
        $saldoRunning = [];

        $awal  = self::MIN_TAHUN;
        $akhir = now()->year + 5;

        $start = Carbon::create($awal, 1, 1);

        $saldoSebelum = (int) Keuangan::where('tipe', 'masuk')
                ->where('tanggal', '<', $start->toDateString())->sum('jumlah')
            - (int) Keuangan::where('tipe', 'keluar')
                ->where('tanggal', '<', $start->toDateString())->sum('jumlah');

        $saldo = $saldoSebelum;

        for ($t = $awal; $t <= $akhir; $t++) {
            $m = (int) Keuangan::where('tipe', 'masuk')->whereYear('tanggal', $t)->sum('jumlah');
            $k = (int) Keuangan::where('tipe', 'keluar')->whereYear('tanggal', $t)->sum('jumlah');

            $saldo += ($m - $k);

            $labels[] = (string) $t;
            $pemasukan[] = $m;
            $pengeluaran[] = $k;
            $saldoRunning[] = $saldo;
        }

        return compact('labels', 'pemasukan', 'pengeluaran', 'saldoRunning');
    }
}
