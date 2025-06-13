<?php

namespace App\Http\Controllers;

use App\Models\MasterSatuan;
use Illuminate\Http\Request;

class MasterSatuanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('units.index',[
            'unit' => MasterSatuan::all()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
    public function edit(MasterSatuan $unit)
    {
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MasterSatuan $unit)
    {
        $validatedData= $request -> validate([
            'jenis_satuan'=>'required',
            'keterangan_satuan'=>'required',
        ]);

        MasterSatuan::where('id',$unit->id)->update($validatedData);
        return redirect('/units')->with('sukses', 'Satuan telah diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
