<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\FonnteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class WargaController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q'));
        $role = (string) $request->get('role', '');
        $aktif = (string) $request->get('aktif', '');

        $query = User::query()->orderBy('name');

        if ($q !== '') {
            $query->where(function ($qq) use ($q) {
                $qq->where('name', 'like', "%{$q}%")
                   ->orWhere('nik', 'like', "%{$q}%")
                   ->orWhere('no_kk', 'like', "%{$q}%")
                   ->orWhere('no_hp', 'like', "%{$q}%");
            });
        }
        if (in_array($role, ['user', 'admin', 'ketua_rw'])) {
            $query->where('role', $role);
        }
        if ($aktif === '1' || $aktif === '0') {
            $query->where('akun_aktif', (bool) $aktif);
        }

        $items = $query->paginate(20)->withQueryString();

        $stats = [
            'total'   => User::count(),
            'aktif'   => User::where('akun_aktif', true)->count(),
            'nonaktif'=> User::where('akun_aktif', false)->count(),
            'warga'   => User::where('role', 'user')->count(),
        ];

        return view('warga.index', compact('items', 'q', 'role', 'aktif', 'stats'));
    }

    public function create()
    {
        return view('warga.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateData($request);

        $passwordPlain = $request->filled('password')
            ? $validated['password']
            : Str::password(10, true, true, false, false);

        User::create(array_merge(
            $validated,
            [
                'password'        => Hash::make($passwordPlain),
                'akun_aktif'      => true,
                'is_kepala_keluarga' => $request->boolean('is_kepala_keluarga'),
                'notif_wa_aktif'  => true,
            ]
        ));

        return redirect()->route('warga.index')
            ->with('success', "Warga berhasil ditambahkan. Kata sandi awal: {$passwordPlain}");
    }

    public function edit(User $warga)
    {
        return view('warga.edit', ['user' => $warga]);
    }

    public function update(Request $request, User $warga)
    {
        $validated = $this->validateData($request, $warga);
        $validated['is_kepala_keluarga'] = $request->boolean('is_kepala_keluarga');
        unset($validated['password']);

        $warga->update($validated);

        return redirect()->route('warga.index')->with('success', 'Data warga berhasil diperbarui.');
    }

    public function deactivate(User $warga)
    {
        if ($warga->id === auth()->id()) {
            return back()->withErrors(['error' => 'Anda tidak bisa menonaktifkan akun sendiri.']);
        }

        $warga->update(['akun_aktif' => false]);

        // Kirim notifikasi WhatsApp jika user punya nomor HP dan notif aktif
        if ($warga->bisaTerimaWa()) {
            $pesan = "Yth. {$warga->name}, akun Sistem RW Anda telah dinonaktifkan oleh Admin/RW. Jika merasa ini keliru, silakan hubungi pengurus RW.";
            app(FonnteService::class)->kirim($warga->no_hp, $pesan, 'akun_dinonaktifkan', $warga);
        }

        return back()->with('success', 'Akun warga dinonaktifkan.');
    }

    public function activate(User $warga)
    {
        $warga->update(['akun_aktif' => true]);
        return back()->with('success', 'Akun warga diaktifkan.');
    }

    public function destroy(User $warga)
    {
        if ($warga->id === auth()->id()) {
            return back()->withErrors(['error' => 'Anda tidak bisa menghapus akun sendiri.']);
        }

        // Kirim notifikasi WhatsApp sebelum soft delete (agar bisa baca no_hp)
        if ($warga->bisaTerimaWa()) {
            $pesan = "Yth. {$warga->name}, akun Sistem RW Anda telah dihapus oleh Admin/RW. Jika merasa ini keliru, silakan hubungi pengurus RW.";
            try {
                app(FonnteService::class)->kirim($warga->no_hp, $pesan, 'akun_dihapus', $warga);
            } catch (\Throwable $e) {
                report($e);
            }
        }

        // Soft delete - menjaga data history tetap ada
        $warga->delete();

        return back()->with('success', "Akun {$warga->name} berhasil dihapus.");
    }

    public function resetPassword(User $warga)
    {
        $passwordBaru = Str::password(10, true, true, false, false);
        $warga->update(['password' => Hash::make($passwordBaru)]);

        return back()->with('success', "Kata sandi {$warga->name} di-reset ke: {$passwordBaru}");
    }

    /* ===== Helpers ===== */

    protected function validateData(Request $request, ?User $exclude = null): array
    {
        return $request->validate([
            'name'           => 'required|string|max:255',
            'nik'            => ['nullable', 'string', 'digits:16', Rule::unique('users', 'nik')->ignore($exclude?->id)],
            'no_kk'          => 'nullable|string|digits:16',
            'email'          => ['nullable', 'email', Rule::unique('users', 'email')->ignore($exclude?->id)],
            'no_hp'          => 'nullable|string|max:20|regex:/^[0-9+\-\s()]+$/',
            'role'           => ['required', Rule::in(['user', 'admin', 'ketua_rw'])],
            'jenis_kelamin'  => ['nullable', Rule::in(['L', 'P'])],
            'tanggal_lahir'  => 'nullable|date',
            'tempat_lahir'   => 'nullable|string|max:100',
            'status_keluarga'=> 'nullable|string|max:30',
            'pekerjaan'      => 'nullable|string|max:100',
            'status_warga'   => ['nullable', Rule::in(['tetap', 'kontrak', 'kos', 'lainnya'])],
            'rt'             => 'nullable|string|max:10',
            'rw'             => 'nullable|string|max:10',
            'no_rumah'       => 'nullable|string|max:10',
            'alamat_detail'  => 'nullable|string|max:500',
            'kode_pos'       => 'nullable|string|max:10',
            'password'       => 'nullable|string|min:6',
        ], [
            'nik.digits' => 'NIK harus 16 angka.',
            'no_kk.digits' => 'Nomor KK harus 16 angka.',
        ]);
    }
}
