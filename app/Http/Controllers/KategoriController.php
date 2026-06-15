<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\KategoriRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class KategoriController extends Controller
{
    public function index()
    {
        $kategori = Kategori::latest()->get();
        return view('categories.index', compact('kategori'));
        
    }
    public function store(KategoriRequest $request)
    {
        // Ambil kode terakhir, misal KT005
        $last = Kategori::orderBy('id', 'desc')->first();
        $lastCode = $last ? (int)substr($last->kode_kategori, 2) : 0;
        $newCode = 'KT' . str_pad($lastCode + 1, 3, '0', STR_PAD_LEFT);

        $kategori = Kategori::create([
            'kode_kategori' => $newCode,
            'nama_kategori' => $request->nama_kategori,
        ]);

        Log::channel('kategori')->info('Kategori berhasil ditambahkan', [
            'kategori_id' => $kategori->id,
            'kode_kategori' => $kategori->kode_kategori,
            'nama_kategori' => $kategori->nama_kategori,
            'user' => ['id' => Auth::id(), 'name' => Auth::user()->name ?? null],
            'ip_address' => request()->ip(),
            'waktu' => now()->toDateTimeString(),
        ]);

        return response()->json(['message' => 'Kategori berhasil ditambahkan.']);
    }
    public function update(KategoriRequest $request, $id)
    {
        $kategori = Kategori::findOrFail($id);

        $kategori->update([
            'nama_kategori' => $request->nama_kategori,
        ]);

        Log::channel('kategori')->info('Kategori berhasil diperbarui', [
            'kategori_id' => $kategori->id,
            'nama_kategori' => $kategori->nama_kategori,
            'user' => ['id' => Auth::id(), 'name' => Auth::user()->name ?? null],
            'ip_address' => request()->ip(),
            'waktu' => now()->toDateTimeString(),
        ]);

        return response()->json(['message' => 'Kategori berhasil diperbarui.']);
    }
    public function destroy($id)
    {
        $kategori = Kategori::find($id);
        if (!$kategori){
           return response()->json([
            'status' => 'error',
            'message' => 'Data kategori tidak ditemukan.'
        ], 404);
        }
        if ($kategori->produk()->exists()) {
        Log::channel('kategori')->warning('Hapus kategori ditolak karena masih digunakan produk', [
            'kategori_id' => $kategori->id,
            'nama_kategori' => $kategori->nama_kategori,
            'user_id' => Auth::id(),
            'ip_address' => request()->ip(),
        ]);

        return response()->json([
            'status' => 'error',
            'message' => 'Kategori tidak dapat dihapus karena masih digunakan dalam data produk.'
        ], 400);
        }
        $kategoriData = $kategori->only(['id', 'kode_kategori', 'nama_kategori']);
        $kategori->delete();

        Log::channel('kategori')->warning('Kategori dihapus', [
            'kategori' => $kategoriData,
            'user' => ['id' => Auth::id(), 'name' => Auth::user()->name ?? null],
            'ip_address' => request()->ip(),
            'waktu' => now()->toDateTimeString(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Kategori berhasil dihapus.'
        ]);
    }

}
