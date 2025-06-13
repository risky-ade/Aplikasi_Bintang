<?php

namespace App\Http\Controllers;

use App\Models\MasterSatuan;
use Illuminate\Http\Request;

class MasterSatuanController extends Controller
{
    public function index()
    {
        return view('units.index',[
            'unit' => MasterSatuan::all()
        ]);
    }
}
