<?php

namespace App\Http\Controllers;

use App\Models\Satuan;
use App\Models\Kategori;
use App\Models\MasterProduk;
use Illuminate\Http\Request;
use App\Models\MasterKategori;
use Illuminate\Support\Facades\File;

use App\Models\HistoriHargaPenjualan;
use Illuminate\Support\Facades\Storage;
use function PHPUnit\Framework\returnSelf;

class MasterProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // return view('master_produk.index',[
        //     'items' => MasterProduk::all()
        // ]);
        $masterProduk = MasterProduk::with(['kategori', 'satuan'])->get();
        return view('master_produk.index', compact('masterProduk'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kategori = Kategori::all();
        $satuan = Satuan::all();
      return view('master_produk.create', compact('satuan','kategori'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
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
            $data['gambar'] = $request->file('gambar')->store('gambar_produk', 'public');
        }
        // if($file=$request->file('gambar')
        // {
        //     $destinationPatch = 'produk_img/';
        //     $imageName=time().'_'.$file->getClientOriginalName();
        //     $file->move($destinationPatch, $imageName);
        //     $data['gambar'] = $imageName;
        // });

        MasterProduk::create($data);
        return redirect('/master_produk')->with('success', 'Produk berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $masterProduk = MasterProduk::findOrFail($id);
        $kategori = Kategori::all();
        $satuan = Satuan::all();
        return view('master_produk.edit', compact('masterProduk', 'kategori', 'satuan'));
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
        // Di controller atau observer saat update produk
        // if ($data->isDirty('harga_jual')) {
        //     HistoriHargaPenjualan::create([
        //         'produk_id' => $request->id,
        //         'sumber' => 'produk',
        //         'harga_lama' => $request->getOriginal('harga_jual'),
        //         'harga_baru' => $request->harga_jual,
        //         'tanggal' => now(),
        //         'keterangan' => 'Update harga master produk',
        //     ]);
        // }
        if ($request->hasFile('gambar')) {
        // Hapus gambar lama jika ada
            if ($masterProduk->gambar) {
                Storage::disk('public')->delete($masterProduk->gambar);
            }

            $data['gambar'] = $request->file('gambar')->store('gambar_produk', 'public');
        }
        
        if ($masterProduk->harga_jual != $request->harga_jual) {
            HistoriHargaPenjualan::create([
                'produk_id' => $masterProduk->id,
                'harga_lama' => $masterProduk->getOriginal('harga_jual'),
                'harga_baru' => $request->harga_jual,
                'sumber' => 'produk',
                'tanggal' => now(),
                'keterangan' => 'Update dari master produk',
            ]);
        }
        $masterProduk->update($data);
        return redirect('/master_produk')->with('success', 'Produk berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $produk = MasterProduk::findOrFail($id);

        if ($produk->penjualanDetail()->exists() || $produk->returPenjualanDetail()->exists()) {
            return response()->json([
                'message' => 'Produk tidak dapat dihapus karena sudah digunakan dalam transaksi.'
            ], 400);
        }

        // Hapus file gambar jika ada
        if($produk->gambar && Storage::disk('public')->exists($produk->gambar)){
            Storage::disk('public')->delete($produk->gambar);
        }
        // if ($produk->gambar && file_exists(public_path($produk->gambar))) {
        //     unlink(public_path($produk->gambar));
        // }

        $produk->delete();

        return response()->json(['message' => 'Produk berhasil dihapus.']);
        // $data = MasterProduk::findOrFail($id);

        // //hapus gambar jika ada

        // if($data->gambar && Storage::disk('public')->exists($data->gambar)){
        //     Storage::disk('public')->delete($data->gambar);
        // }
        // $data->delete();

        // return redirect('/master_produk')->with('success', 'Produk berhasil dihapus');
    }
    //fungsi search untuk cari produk pada kolom add penjualan
    public function search(Request $request)
    {
        $term = $request->term;
        $produk = MasterProduk::where('nama_produk', 'LIKE', "%$term%")->get();

        $results = [];
        foreach ($produk as $item) {
            $results[] = [
                'id' => $item->id,
                'text' => $item->nama_produk,
                'harga_jual' => $item->harga_jual
            ];
        }

        return response()->json(['results' => $results]);
    }
}
