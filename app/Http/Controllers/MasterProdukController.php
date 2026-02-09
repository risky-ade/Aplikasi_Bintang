<?php

namespace App\Http\Controllers;


use App\Models\Satuan;
use App\Models\Kategori;
use App\Models\MasterProduk;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Models\HistoriHargaPenjualan;
use Illuminate\Support\Facades\Storage;
use function PHPUnit\Framework\returnSelf;

class MasterProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MasterProduk::with(['kategori', 'satuan']);

        if ($request->status === 'aktif') {
            $query->where('is_active', true);
        } elseif ($request->status === 'nonaktif') {
            $query->where('is_active', false);
        }
        if ($request->filled('nama')) {
            $query->where('nama_produk', 'like', '%' . $request->nama . '%');
        }

        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        $masterProduk = $query
            ->orderBy('nama_produk')
            ->paginate(10)
            ->withQueryString();

        $kategoris = Kategori::orderBy('nama_kategori')->get();

        // $masterProduk = $query->get();
        return view('master_produk.index', compact('masterProduk','kategoris'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {   
        // $masterProduk= MasterProduk::all();
        $kategori = Kategori::all();
        $satuan = Satuan::all();
        // $isLocked = $masterProduk->isUsedInTransaction();
      return view('master_produk.create', compact('satuan','kategori'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_produk' =>'required|unique:master_produk,nama_produk',
            'kategori_id' =>'required|exists:kategori,id',
            'satuan_id' =>'required',
            'harga_dasar' =>'required|numeric',
            'harga_jual' =>'required|numeric',
            'include_pajak' =>'required',
            'stok' =>'required|integer',
            'gambar'=>'nullable|image|max:2048'
        ], [
            'nama_produk.unique' => 'Nama produk sudah digunakan. Silakan gunakan nama lain.',
        ]);
        $data = $request->all();

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('gambar_produk', 'public');
        }

        $produk= MasterProduk::create($data);

        Log::channel('produk')->info('Produk berhasil ditambahkan', [
            'produk_id' => $produk->id,
            'nama'=> $produk->nama_produk,
            'harga_jual'=> $produk->harga_jual,
            'user_id' => Auth::id(),
            'ip'=> $request->ip(),
        ]);
        return redirect('/master_produk')->with('success', 'Produk berhasil ditambahkan');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }
    public function checkDuplicate(Request $request)
    {
        $nama = $request->nama_produk;

        $exists = MasterProduk::where('nama_produk', $nama)->exists();

        return response()->json([
            'exists' => $exists
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $masterProduk = MasterProduk::findOrFail($id);
        
        $kategori = Kategori::all();
        $satuan   = Satuan::all();
        $isLocked = $masterProduk->isUsedInTransaction();

        return view('master_produk.edit', compact('masterProduk', 'kategori', 'satuan','isLocked'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $masterProduk = MasterProduk::findOrFail($id);
        $request->validate([
            'nama_produk' =>'required',
            'kategori_id' =>'required|exists:kategori,id',
            'satuan_id' =>'required',
            'harga_dasar' =>'required|numeric',
            'harga_jual' =>'required|numeric',
            'include_pajak' =>'required',
            'stok' =>'required|integer',
            'gambar'=>'nullable|image|max:2048'
        ]);
        $data = $request->all();

        if ($request->hasFile('gambar')) {
        // Hapus gambar lama jika ada
            if ($masterProduk->gambar) {
                Storage::disk('public')->delete($masterProduk->gambar);
            }

            $data['gambar'] = $request->file('gambar')->store('gambar_produk', 'public');
        }
        
        // if ($masterProduk->harga_jual != $request->harga_jual) {
        //     HistoriHargaPenjualan::create([
        //         'produk_id' => $masterProduk->id,
        //         'harga_lama' => $masterProduk->getOriginal('harga_jual'),
        //         'harga_baru' => $request->harga_jual,
        //         'sumber' => 'produk',
        //         'tanggal' => now(),
        //         'keterangan' => 'Update dari master produk',
        //     ]);

        //     Log::channel('produk')->info('Perubahan harga produk', [
        //         'produk_id' => $masterProduk->id,
        //         'nama' => $masterProduk->nama_produk,
        //         'harga_lama' => $masterProduk->harga_jual,
        //         'harga_baru' => $request->harga_jual,
        //         'user_id' => Auth::id(),
        //     ]);
        // }
        $masterProduk->update($data);

        Log::channel('produk')->info('Produk diperbarui', [
            'produk_id' => $masterProduk->id,
            'nama' => $masterProduk->nama_produk,
            'user_id' => Auth::id(),
        ]);
        return redirect('/master_produk')->with('success', 'Produk berhasil diperbarui');
    }
 
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $produk = MasterProduk::findOrFail($id);

        // if ($produk->penjualanDetail()->exists() || $produk->returPenjualanDetail()->exists()) {
        //     return response()->json([
        //         'message' => 'Produk tidak dapat dihapus karena sudah digunakan dalam transaksi.'
        //     ], 400);
        // }
        if ($produk->isUsedInTransaction()) {
            Log::channel('produk')->warning('Hapus produk ditolak (sudah ada transaksi)', [
                'produk_id' => $produk->id,
                'nama' => $produk->nama_produk,
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'message' => 'Produk tidak bisa dihapus karena sudah ada riwayat transaksi. Gunakan Nonaktif.'
            ], 400);
        }

        // Hapus file gambar jika ada
        if($produk->gambar && Storage::disk('public')->exists($produk->gambar)){
            Storage::disk('public')->delete($produk->gambar);
        }


        $produk->delete();

        Log::channel('produk')->info('Produk dihapus', [
            'produk_id' => $produk->id,
            'nama' => $produk->nama_produk,
            'user_id' => Auth::id(),
        ]);
        return response()->json(['message' => 'Produk berhasil dihapus.']);

    }
    //fungsi search untuk cari produk pada kolom add penjualan
    public function search(Request $request)
    {
        $term = $request->term;
        $produk = MasterProduk::where('is_active', true)
            ->where('nama_produk', 'LIKE', "%$term%")
            ->get();

        $results = [];
        foreach ($produk as $item) {
            $results[] = [
                'id' => $item->id,
                'text' => $item->nama_produk,
                'harga_jual' => $item->harga_jual,
                'harga_dasar'=> $item->harga_dasar,
            ];
        }
        
        return response()->json(['results' => $results]);
    }

    public function toggleActive($id)
    {
        $produk = MasterProduk::findOrFail($id);

        $produk->update([
            'is_active' => !$produk->is_active
        ]);

        Log::channel('produk')->info('Status produk diubah', [
            'produk_id' => $produk->id,
            'nama' => $produk->nama_produk,
            'status' => $produk->is_active ? 'aktif' : 'nonaktif',
            'user_id' => Auth::id(),
        ]);

        return response()->json([
            'message' => $produk->is_active ? 'Produk diaktifkan.' : 'Produk dinonaktifkan.'
        ]);
    }

}
