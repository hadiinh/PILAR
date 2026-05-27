<?php

namespace App\Http\Controllers;

use App\Models\Foto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FotoController extends Controller
{
    public function index()
    {
        $fotos = Foto::latest()->get();

        return view('foto.index', compact('fotos'));
    }

    public function create()
    {
        return view('foto.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'deskripsi' => 'nullable|string|max:1000',
            'gambar' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $gambar = $request->file('gambar')->store('foto', 'public');

        Foto::create([
            'judul' => $request->judul,
            'kategori' => $request->kategori,
            'deskripsi' => $request->deskripsi,
            'gambar' => $gambar
        ]);

        return redirect('/foto');
    }

    public function show($id)
    {
        $foto = Foto::findOrFail($id);

        return view('foto.show', compact('foto'));
    }

    public function edit($id)
    {
        $foto = Foto::findOrFail($id);

        return view('foto.edit', compact('foto'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'deskripsi' => 'nullable|string|max:1000',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $foto = Foto::findOrFail($id);

        if ($request->hasFile('gambar')) {
            if ($foto->gambar && Storage::disk('public')->exists($foto->gambar)) {
                Storage::disk('public')->delete($foto->gambar);
            }
            $gambar = $request->file('gambar')->store('foto', 'public');
            $foto->gambar = $gambar;
        }

        $foto->judul = $request->judul;
        $foto->kategori = $request->kategori;
        $foto->deskripsi = $request->deskripsi;
        $foto->save();

        return redirect('/foto')->with('success', 'Foto berhasil diperbarui');
    }

    public function destroy($id)
    {
        $foto = Foto::findOrFail($id);

        if ($foto->gambar && Storage::disk('public')->exists($foto->gambar)) {
            Storage::disk('public')->delete($foto->gambar);
        }

        $foto->delete();

        return redirect('/foto')->with('success', 'Foto berhasil dihapus');
    }

    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'selected_fotos' => 'required|array|min:1',
            'selected_fotos.*' => 'integer|exists:fotos,id',
        ]);

        $fotos = Foto::whereIn('id', $validated['selected_fotos'])->get();

        foreach ($fotos as $foto) {
            if ($foto->gambar && Storage::disk('public')->exists($foto->gambar)) {
                Storage::disk('public')->delete($foto->gambar);
            }
            $foto->delete();
        }

        return redirect('/foto')->with('success', 'Foto berhasil dihapus');
    }
}