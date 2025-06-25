<?php

namespace App\Http\Controllers;

use App\Models\Satuan;
use Illuminate\Http\Request;

class SatuanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('units.index',[
            'unit' => Satuan::all()
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
        Satuan::create($request->all());

        return redirect('/units');
        // ->with('success', 'satuan berhasil dibuat');
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

    public function getById($id)
    {
        $ms = Satuan::find($id);
        $response['success'] = true;
        $response['data'] = $ms;
        // dd($response);
        return response()->json($response);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Satuan $unit)
    {

        $id = $request->idSatuan;
        // dd($request->all());

        $unit = Satuan::find($id)->update([
            'id'=>$request->idSatuan,
            'jenis_satuan'=> $request->jenis_satuan_up,
            'keterangan_satuan'=>$request->keterangan_satuan_up
        ]);

        
        return redirect('/units');
        // ->with('sukses', 'Satuan telah diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $unit = Satuan::find($id);
        if (!$unit){
           return response()->json([
            'status' => 'error',
            'message' => 'Data satuan tidak ditemukan.'
        ], 404);
        }
        if ($unit->produk()->exists()) {
        return response()->json([
            'status' => 'error',
            'message' => 'Satuan tidak dapat dihapus karena masih digunakan dalam data produk.'
        ], 400);
        }
        $unit->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Satuan berhasil dihapus.'
        ]);
    }
}
