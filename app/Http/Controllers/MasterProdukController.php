<?php

namespace App\Http\Controllers;

use App\Models\Satuan;
use App\Models\Kategori;
use App\Models\MasterProduk;
use Illuminate\Http\Request;
use App\Models\MasterKategori;
use Illuminate\Support\Facades\File;

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
        $masterKategori = Kategori::all();
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
        if ($request->hasFile('gambar')) {
        // Hapus gambar lama jika ada
        if ($masterProduk->gambar) {
            Storage::disk('public')->delete($masterProduk->gambar);
        }

        $data['gambar'] = $request->file('gambar')->store('gambar_produk', 'public');
    }
    $masterProduk->update($data);
        return redirect('/master_produk')->with('success', 'Produk berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = MasterProduk::findOrFail($id);
        //hapus gambar jika ada

        if($data->gambar && Storage::disk('public')->exists($data->gambar)){
            Storage::disk('public')->delete($data->gambar);
        }
        $data->delete();

        return redirect('/master_produk')->with('success', 'Produk berhasil dihapus');
    }

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
