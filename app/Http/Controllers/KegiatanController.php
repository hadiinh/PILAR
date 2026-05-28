<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Services\NotifikasiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class KegiatanController extends Controller
{
    public function __construct(protected NotifikasiService $notifikasi) {}

    public function index()
    {
        $kegiatans = Kegiatan::latest()->get();
        return view('kegiatan.index', compact('kegiatans'));
    }

    public function create()
    {
        return view('kegiatan.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul'     => 'required|string|max:255',
            'deskripsi' => 'required|string|max:2000',
            'tanggal'   => 'required|date',
            'status'    => 'nullable|in:baru,diproses,selesai',
            'gambar'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $gambar = null;
        if ($request->hasFile('gambar')) {
            $gambar = $request->file('gambar')->store('kegiatan', 'public');
        }

        $kegiatan = Kegiatan::create([
            'judul'     => $validated['judul'],
            'slug'      => Str::slug($validated['judul']).'-'.Str::lower(Str::random(5)),
            'deskripsi' => $validated['deskripsi'],
            'tanggal'   => $validated['tanggal'],
            'status'    => $validated['status'] ?? 'baru',
            'gambar'    => $gambar,
        ]);

        try {
            $this->notifikasi->broadcastKegiatanBaru($kegiatan);
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect('/kegiatan')->with('success', 'Kegiatan berhasil ditambahkan dan notifikasi WA dikirim.');
    }

    public function show(Kegiatan $kegiatan)
    {
        return view('kegiatan.show', compact('kegiatan'));
    }

    public function edit(Kegiatan $kegiatan)
    {
        return view('kegiatan.edit', compact('kegiatan'));
    }

    public function update(Request $request, Kegiatan $kegiatan)
    {
        $validated = $request->validate([
            'judul'     => 'required|string|max:255',
            'deskripsi' => 'required|string|max:2000',
            'tanggal'   => 'required|date',
            'status'    => 'required|in:baru,diproses,selesai',
            'gambar'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('gambar')) {
            if ($kegiatan->gambar && Storage::disk('public')->exists($kegiatan->gambar)) {
                Storage::disk('public')->delete($kegiatan->gambar);
            }
            $validated['gambar'] = $request->file('gambar')->store('kegiatan', 'public');
        }

        $kegiatan->update($validated);

        return redirect('/kegiatan')->with('success', 'Kegiatan berhasil diperbarui.');
    }

    public function destroy(Kegiatan $kegiatan)
    {
        if ($kegiatan->gambar && Storage::disk('public')->exists($kegiatan->gambar)) {
            Storage::disk('public')->delete($kegiatan->gambar);
        }
        $kegiatan->delete();

        return redirect('/kegiatan')->with('success', 'Kegiatan berhasil dihapus.');
    }
}
