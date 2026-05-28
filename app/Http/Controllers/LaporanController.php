<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use App\Services\NotifikasiService;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function __construct(protected NotifikasiService $notifikasi) {}

    public function index()
    {
        $user      = auth()->user();
        $isManager = in_array($user->role, ['ketua_rw', 'admin']);

        $query = Laporan::with('user');
        if (!$isManager) {
            $query->where('user_id', $user->id);
        }

        $laporans      = (clone $query)->latest()->get();
        $countBaru     = (clone $query)->where('status', 'baru')->count();
        $countDiproses = (clone $query)->where('status', 'diproses')->count();
        $countSelesai  = (clone $query)->where('status', 'selesai')->count();

        return view('laporan.index', compact('laporans', 'countBaru', 'countDiproses', 'countSelesai'));
    }

    public function create()
    {
        return view('laporan.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul'     => 'required|string|max:255',
            'tanggal'   => 'required|date',
            'jam'       => 'required',
            'lokasi'    => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:2000',
            'foto'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('laporan', 'public');
        }

        $laporan = Laporan::create([
            'user_id'   => auth()->id(),
            'judul'     => $validated['judul'],
            'tanggal'   => $validated['tanggal'],
            'jam'       => $validated['jam'],
            'lokasi'    => $validated['lokasi'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'foto'      => $fotoPath,
            'status'    => 'baru',
        ]);

        // Kirim notifikasi WA ke admin & ketua RW
        try {
            $laporan->load('user');
            $this->notifikasi->notifLaporanBaruKePengurus($laporan);
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()->route('laporan.index')
            ->with('laporan_success', 'Laporan berhasil dikirim. Pengurus RW akan segera meninjau.');
    }

    public function show(Laporan $laporan)
    {
        $user      = auth()->user();
        $isManager = in_array($user->role, ['ketua_rw', 'admin']);

        if (!$isManager && $laporan->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke laporan ini');
        }

        return view('laporan.show', compact('laporan'));
    }

    public function edit(Laporan $laporan)
    {
        return view('laporan.edit', compact('laporan'));
    }

    public function update(Request $request, Laporan $laporan)
    {
        $request->validate(['status' => 'required|in:baru,diproses,selesai']);

        $statusLama = $laporan->status;
        $laporan->update(['status' => $request->status]);

        // Notifikasi ke pelapor saat status berubah ke "diproses" atau "selesai"
        if ($statusLama !== $laporan->status && in_array($laporan->status, ['diproses', 'selesai'])) {
            try {
                $laporan->load('user');
                $this->notifikasi->notifStatusLaporanKePelapor($laporan);
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return redirect()->route('laporan.index')->with('success', 'Status laporan berhasil diperbarui.');
    }
}
