<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    public function index()
    {
        $jadwals = Jadwal::orderBy('tanggal', 'desc')->orderBy('jam', 'desc')->get();
        return view('jadwal.index', compact('jadwals'));
    }

    public function create()
    {
        return view('jadwal.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul'     => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:2000',
            'tanggal'   => 'required|date',
            'jam'       => 'required',
            'lokasi'    => 'required|string|max:255',
            'kategori'  => 'nullable|string|max:50',
        ]);

        Jadwal::create([
            'judul'     => $validated['judul'],
            'deskripsi' => $validated['deskripsi'] ?? '',
            'tanggal'   => $validated['tanggal'],
            'jam'       => $validated['jam'],
            'lokasi'    => $validated['lokasi'],
            'kategori'  => $validated['kategori'] ?? null,
            'status'    => 'Aktif',
        ]);

        return redirect()->route('jadwal.index')->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function show(Jadwal $jadwal)
    {
        return view('jadwal.show', compact('jadwal'));
    }

    public function edit(Jadwal $jadwal)
    {
        return view('jadwal.edit', compact('jadwal'));
    }

    public function update(Request $request, Jadwal $jadwal)
    {
        $validated = $request->validate([
            'judul'     => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:2000',
            'tanggal'   => 'required|date',
            'jam'       => 'required',
            'lokasi'    => 'required|string|max:255',
            'kategori'  => 'nullable|string|max:50',
        ]);

        $jadwal->update($validated + ['deskripsi' => $validated['deskripsi'] ?? '']);

        return redirect()->route('jadwal.index')->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function destroy(Jadwal $jadwal)
    {
        $jadwal->delete();
        return redirect()->route('jadwal.index')->with('success', 'Jadwal berhasil dihapus.');
    }
}
