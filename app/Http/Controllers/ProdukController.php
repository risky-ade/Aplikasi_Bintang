<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\MasterSatuan;
use Illuminate\Http\Request;
use App\Models\MasterKategori;
use App\Models\MasterProduk;

class ProdukController extends Controller
{
    private $model;

    public function __construct()
    {
        $this->model = new Produk();
    }
    /**
     * Display a listing of the resource.
     */
    public function getProduk()
    {
        return view('products.index',[
            'produk'=> Produk::all(),
            'kategori'=>MasterKategori::all(),
            'satuan'=> MasterSatuan::all()
        ]);
        // $produk = $this->model->getProduk();
        // return view('products.index', compact('produk'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('products.create',[
            'm_produk'=>MasterProduk::all()
        ]);
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
    public function edit(Produk $produk)
    {
        return view('master_produk.edit',[
            'produk'=>$produk,
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
