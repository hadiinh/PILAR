<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'rt'       => 'nullable|string|max:10',
            'no_rumah' => 'nullable|string|max:10',
        ]);

        $user->name     = $validated['name'];
        $user->rt       = $validated['rt'] ?? null;
        $user->no_rumah = $validated['no_rumah'] ?? null;
        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
