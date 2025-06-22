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
        $request -> validate([
            'jenis_satuan'=>'required',
            'keterangan_satuan'=> 'required'
        ]);
        MasterSatuan::create($request->all());

        return redirect('/units')->with('sukses', 'satuan berhasil dibuat');
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

    public function getById($id)
    {
        $ks = MasterSatuan::find($id);
        $response['success'] = true;
        $response['data'] = $ks;
        // dd($response);
        return response()->json($response);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MasterSatuan $unit)
    {
        // $request -> validate([
        //     'jenis_satuan'=>'required',
        //     'keterangan_satuan'=>'required',
        // ]);

        $id = $request->idSatuan;
        $data = MasterSatuan::find($id)->update([
            'id'=>$request->idSatuan,
            'jenis_satuan'=> $request->jenis_satuan_up,
            'keterangan_satuan'=>$request->keterangan_satuan_up
        ]);

        
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
