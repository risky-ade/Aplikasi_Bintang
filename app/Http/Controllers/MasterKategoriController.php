<?php

namespace App\Http\Controllers;

use App\Models\MasterKategori;
use Illuminate\Http\Request;

class MasterKategoriController extends Controller
{
        public function index()
    {
        return view('categories.index',[
            'category' => MasterKategori::all()
        ]);
    }

    public function store(Request $request)
    {
         $request -> validate([
            'kode_kategori'=>'required',
            'nama_kategori'=> 'required'
        ]);
        MasterKategori::create($request->all());

        return redirect('/categories');
        // ->with('success', 'satuan berhasil dibuat');
    }
    // edit data by get id
     public function edit($id)
    {
        $mk = MasterKategori::find($id);
        $response['success'] = true;
        $response['data'] = $mk;
        // dd($response);
        return response()->json($response);
    }
    public function update(Request $request, MasterKategori $category)
    {
        $id = $request->idKategori;
        // dd($request->all());
        $data = MasterKategori::find($id)->update([
            'id'=>$request->idKategori,
            'kode_kategori'=> $request->kode_kategori_up,
            'nama_kategori'=>$request->nama_kategori_up
        ]);

        
        return redirect('/categories');
        // ->with('sukses', 'Satuan telah diupdate');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $kategori = MasterKategori::find($id);
        if (!$kategori){
           return response()->json([
            'status' => 'error',
            'message' => 'Data kategori tidak ditemukan.'
        ], 404);
        }
        if ($kategori->produk()->exists()) {
        return response()->json([
            'status' => 'error',
            'message' => 'Kategori tidak dapat dihapus karena masih digunakan dalam data produk.'
        ], 400);
        }
        $kategori->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Kategori berhasil dihapus.'
        ]);
    }
}
