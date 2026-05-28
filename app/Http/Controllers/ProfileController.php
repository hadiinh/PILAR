<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'no_hp'          => 'nullable|string|max:20|regex:/^[0-9+\-\s()]+$/',
            'status_warga'   => ['nullable', Rule::in(['tetap', 'kontrak', 'kos', 'lainnya'])],
            'notif_wa_aktif' => 'nullable|boolean',

            'provinsi_id'    => 'nullable|string|max:10',
            'provinsi_nama'  => 'nullable|string|max:100',
            'kota_id'        => 'nullable|string|max:10',
            'kota_nama'      => 'nullable|string|max:100',
            'kecamatan_id'   => 'nullable|string|max:15',
            'kecamatan_nama' => 'nullable|string|max:100',
            'kelurahan_id'   => 'nullable|string|max:20',
            'kelurahan_nama' => 'nullable|string|max:100',
            'alamat_detail'  => 'nullable|string|max:500',
            'rt'             => 'nullable|string|max:10',
            'rw'             => 'nullable|string|max:10',
            'no_rumah'       => 'nullable|string|max:10',
            'kode_pos'       => 'nullable|string|max:10',
        ], [
            'no_hp.regex' => 'Format nomor HP tidak valid.',
        ]);

        $validated['notif_wa_aktif'] = $request->boolean('notif_wa_aktif');

        if (empty($validated['rw'])) {
            $validated['rw'] = $user->rw ?? '016';
        }

        $user->fill($validated)->save();

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function changePassword(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'password_lama'         => 'required|string',
            'password_baru'         => 'required|string|min:6|confirmed',
            'password_baru_confirmation' => 'required|string',
        ], [
            'password_baru.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        if (!Hash::check($request->password_lama, $user->password)) {
            throw ValidationException::withMessages([
                'password_lama' => 'Kata sandi lama tidak sesuai.',
            ]);
        }

        $user->update(['password' => Hash::make($request->password_baru)]);

        return back()->with('success', 'Kata sandi berhasil diubah.');
    }
}
