<?php

namespace App\Http\Controllers;

use App\Models\ProfilePerusahaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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

        $profil = ProfilePerusahaan::firstOrCreate(['id' => 1]);
        $before = $profil->only(['nama_perusahaan', 'email', 'telepon', 'alamat', 'nama_bank', 'no_rekening']);
        $profil->update($data);

        Log::channel('profil')->info('Profil perusahaan berhasil diperbarui', [
            'profil_id' => $profil->id,
            'before' => $before,
            'after' => $profil->only(['nama_perusahaan', 'email', 'telepon', 'alamat', 'nama_bank', 'no_rekening']),
            'user' => ['id' => Auth::id(), 'name' => Auth::user()->name ?? null],
            'ip_address' => request()->ip(),
            'waktu' => now()->toDateTimeString(),
        ]);

        return back()->with('success', 'Profil perusahaan berhasil diperbarui.');
    }
}
