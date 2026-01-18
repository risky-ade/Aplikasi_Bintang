<?php

namespace App\Http\Controllers;

use App\Models\ProfilePerusahaan;
use Illuminate\Http\Request;

class ProfilePerusahaanController extends Controller
{
    public function edit()
    {
        $profil = ProfilePerusahaan::firstOrCreate(['id' => 1]);
        return view('profiles.edit', compact('profil'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'nama_perusahaan' => 'required|string|max:255',
            'email'           => 'nullable|email',
            'telepon'         => 'nullable|string|max:30',
            'alamat'          => 'nullable|string',
            'nama_bank'       => 'nullable|string|max:100',
            'no_rekening'     => 'nullable|string|max:100',
        ]);

        ProfilePerusahaan::updateOrCreate(['id' => 1], $data);

        return back()->with('success', 'Profil perusahaan berhasil diperbarui.');
    }
}
