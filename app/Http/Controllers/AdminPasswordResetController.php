<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\PasswordResetRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminPasswordResetController extends Controller
{
    public function index()
    {
        $requests = PasswordResetRequest::latest()->get();
        return view('settings.password_reset_requests.index', compact('requests'));
    }

    public function reset(Request $request, PasswordResetRequest $req)
    {
        if ($req->status !== 'pending') {
            return back()->with('error','Request sudah diproses.');
        }

        $request->validate([
            'new_password' => ['required','min:6','confirmed'],
        ]);

        if (!$req->user_id) {
            return back()->with('error','User tidak ditemukan untuk request ini.');
        }

        $user = User::findOrFail($req->user_id);

        $user->update([
            'password' => Hash::make($request->new_password),
            'force_password_change' => true, 
        ]);

        $req->update([
            'status' => 'done',
            'handled_at' => now(),
            'handled_by' => Auth::id(),
        ]);

        return back()->with('success','Password berhasil direset.');
    }

    public function reject(PasswordResetRequest $req)
    {
        if ($req->status !== 'pending') {
            return back()->with('error','Request sudah diproses.');
        }

        $req->update([
            'status' => 'rejected',
            'handled_at' => now(),
            'handled_by' => Auth::id(),
        ]);

        return back()->with('success','Request ditolak.');
    }

    public function destroy($id)
    {
        $req = PasswordResetRequest::findOrFail($id);
        $req->delete();

        return response()->json(['message' => 'Request berhasil dihapus.']);
    }

    /**
     * Hapus semua request yang sudah diproses agar tidak menumpuk
     * Sesuaikan kondisi "sudah diproses" dengan kolom di tabel kamu.
     */
    public function destroyProcessed()
    {
        // contoh A: pakai kolom processed_at
        // $deleted = PasswordResetRequest::whereNotNull('processed_at')->delete();

        // contoh B: pakai kolom status
        $deleted = PasswordResetRequest::where('status', 'done')->delete();

        return response()->json([
            'message' => "Berhasil menghapus {$deleted} request yang sudah diproses."
        ]);
    }
}
