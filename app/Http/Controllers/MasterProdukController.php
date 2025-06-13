<?php

namespace App\Http\Controllers;

use App\Models\MasterKategori;
use App\Models\MasterProduk;
use App\Models\MasterSatuan;
use Illuminate\Http\Request;

class MasterProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('master_produk.index',[
            'items' => MasterProduk::all()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
      return view('master_produk.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
    public function edit(MasterProduk $m_produk)
    {
        return view('master_produk.edit',[
            'm_produk'=>$m_produk,
            'kategori'=> MasterKategori::all(),
            'satuan'=> MasterSatuan::all()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
