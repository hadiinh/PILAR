<?php

namespace App\Http\Controllers;

use App\Models\Keuangan;
use Illuminate\Http\Request;

class KeuanganController extends Controller
{
    public function index()
    {
        $data = Keuangan::orderBy('tanggal', 'desc')->orderBy('id', 'desc')->get();

        $totalMasuk  = (int) Keuangan::where('tipe', 'masuk')->sum('jumlah');
        $totalKeluar = (int) Keuangan::where('tipe', 'keluar')->sum('jumlah');
        $saldo       = $totalMasuk - $totalKeluar;

        return view('keuangan.index', compact('data', 'saldo', 'totalMasuk', 'totalKeluar'));
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
}
