<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DataWargaController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q'));
        $statusRumah = (string) $request->get('status_rumah', '');
        $jenisKelamin = (string) $request->get('jenis_kelamin', '');

        $query = User::query()->orderBy('name');

        if ($q !== '') {
            $query->where(function ($qq) use ($q) {
                $qq->where('name', 'like', "%{$q}%")
                   ->orWhere('nik', 'like', "%{$q}%")
                   ->orWhere('no_kk', 'like', "%{$q}%")
                   ->orWhere('no_hp', 'like', "%{$q}%");
            });
        }
        if (in_array($statusRumah, ['tetap', 'kontrak'])) {
            $query->where('status_rumah', $statusRumah);
        }
        if (in_array($jenisKelamin, ['L', 'P'])) {
            $query->where('jenis_kelamin', $jenisKelamin);
        }

        $items = $query->paginate(20)->withQueryString();

        $stats = [
            'total'  => User::count(),
            'tetap'  => User::where('status_rumah', 'tetap')->count(),
            'kontrak'=> User::where('status_rumah', 'kontrak')->count(),
            'laki'   => User::where('jenis_kelamin', 'L')->count(),
            'perempuan' => User::where('jenis_kelamin', 'P')->count(),
        ];

        return view('data-warga.index', compact('items', 'q', 'statusRumah', 'jenisKelamin', 'stats'));
    }

    public function create()
    {
        return view('data-warga.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateData($request);

        User::create(array_merge($validated, [
            'role'                 => 'user',
            'akun_aktif'           => true,
            'is_kepala_keluarga'   => $request->boolean('is_kepala_keluarga'),
            'notif_wa_aktif'       => true,
        ]));

        return redirect()->route('data-warga.index')
            ->with('success', 'Data warga berhasil ditambahkan.');
    }

    public function edit(User $data_warga)
    {
        return view('data-warga.edit', ['user' => $data_warga]);
    }

    public function update(Request $request, User $data_warga)
    {
        $validated = $this->validateData($request, $data_warga);
        $validated['is_kepala_keluarga'] = $request->boolean('is_kepala_keluarga');
        unset($validated['password']);

        $data_warga->update($validated);

        return redirect()->route('data-warga.index')->with('success', 'Data warga berhasil diperbarui.');
    }

    public function destroy(User $data_warga)
    {
        $data_warga->delete();

        return back()->with('success', "Data {$data_warga->name} berhasil dihapus.");
    }

    protected function validateData(Request $request, ?User $exclude = null): array
    {
        return $request->validate([
            'name'              => 'required|string|max:255',
            'nik'               => ['nullable', 'string', 'digits:16', Rule::unique('users', 'nik')->ignore($exclude?->id)],
            'no_kk'             => 'nullable|string|digits:16',
            'email'             => ['nullable', 'email', Rule::unique('users', 'email')->ignore($exclude?->id)],
            'no_hp'             => 'nullable|string|max:20|regex:/^[0-9+\-\s()]+$/',
            'jenis_kelamin'     => ['nullable', Rule::in(['L', 'P'])],
            'tanggal_lahir'     => 'nullable|date',
            'tempat_lahir'      => 'nullable|string|max:100',
            'status_keluarga'  => 'nullable|string|max:30',
            'pekerjaan'         => 'nullable|string|max:100',
            'status_warga'      => ['nullable', Rule::in(['tetap', 'kontrak', 'kos', 'lainnya'])],
            'status_rumah'      => ['nullable', Rule::in(['tetap', 'kontrak'])],
            'rt'                => 'nullable|string|max:10',
            'rw'                => 'nullable|string|max:10',
            'no_rumah'          => 'nullable|string|max:10',
            'alamat_detail'     => 'nullable|string|max:500',
            'kode_pos'          => 'nullable|string|max:10',
        ], [
            'nik.digits'   => 'NIK harus 16 angka.',
            'no_kk.digits' => 'Nomor KK harus 16 angka.',
        ]);
    }
}
