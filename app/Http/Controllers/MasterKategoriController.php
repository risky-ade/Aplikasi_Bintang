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
}
