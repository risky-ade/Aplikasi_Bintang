<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    private $model;

    public function __construct()
    {
        $this->model = new Produk();
    }


    public function getProduk()
    {
        $data = $this->model->getProduk();
        return view('items.index', compact('data'));
    }

    public function tambahProduk()
    {
        return view('items.create');
    }
    public function editProduk()
    {
        return view('items.edit');
    }
}
