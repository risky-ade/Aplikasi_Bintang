<?php

namespace App\Http\Controllers;

use App\Models\Satuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SatuanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $satuans = Satuan::all();
        return view('units.index', compact('satuans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'jenis_satuan' => 'required|unique:satuan,jenis_satuan',
            'keterangan_satuan' => 'nullable|string',
        ]);

        $satuan = Satuan::create($request->all());

        Log::channel('satuan')->info('Satuan berhasil ditambahkan', [
            'satuan_id' => $satuan->id,
            'jenis_satuan' => $satuan->jenis_satuan,
            'user' => ['id' => Auth::id(), 'name' => Auth::user()->name ?? null],
            'ip_address' => request()->ip(),
            'waktu' => now()->toDateTimeString(),
        ]);

        return redirect()->back()->with('success_add', 'Satuan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }
    

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $satuan = Satuan::findOrFail($id);
        $request->validate([
            'jenis_satuan' => 'required|unique:satuan,jenis_satuan,' . $id,
            'keterangan_satuan' => 'nullable|string',
        ]);

        $before = $satuan->only(['jenis_satuan', 'keterangan_satuan']);
        $satuan->update($request->all());

        Log::channel('satuan')->info('Satuan berhasil diperbarui', [
            'satuan_id' => $satuan->id,
            'before' => $before,
            'after' => $satuan->only(['jenis_satuan', 'keterangan_satuan']),
            'user' => ['id' => Auth::id(), 'name' => Auth::user()->name ?? null],
            'ip_address' => request()->ip(),
            'waktu' => now()->toDateTimeString(),
        ]);

        return redirect()->back()->with('success_update', 'Satuan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $unit = Satuan::find($id);
        if (!$unit){
           return response()->json([
            'status' => 'error',
            'message' => 'Data satuan tidak ditemukan.'
        ], 404);
        }
        if ($unit->produk()->exists()) {
        Log::channel('satuan')->warning('Hapus satuan ditolak karena masih digunakan produk', [
            'satuan_id' => $unit->id,
            'jenis_satuan' => $unit->jenis_satuan,
            'user_id' => Auth::id(),
            'ip_address' => request()->ip(),
        ]);

        return response()->json([
            'status' => 'error',
            'message' => 'Satuan tidak dapat dihapus karena masih digunakan dalam data produk.'
        ], 400);
        }
        $unitData = $unit->only(['id', 'jenis_satuan', 'keterangan_satuan']);
        $unit->delete();

        Log::channel('satuan')->warning('Satuan dihapus', [
            'satuan' => $unitData,
            'user' => ['id' => Auth::id(), 'name' => Auth::user()->name ?? null],
            'ip_address' => request()->ip(),
            'waktu' => now()->toDateTimeString(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Satuan berhasil dihapus.'
        ]);
    }
}
