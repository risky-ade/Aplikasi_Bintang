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
        $satuans = Satuan::all();
        return view('units.index', compact('satuans'));
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
        $request->validate([
            'jenis_satuan' => 'required|unique:satuan,jenis_satuan',
            'keterangan_satuan' => 'nullable|string',
        ]);

        Satuan::create($request->all());
        return redirect()->back()->with('success_add', 'Satuan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }
    

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $satuan = Satuan::findOrFail($id);
        $request->validate([
            'jenis_satuan' => 'required|unique:satuan,jenis_satuan,' . $id,
            'keterangan_satuan' => 'nullable|string',
        ]);

        $satuan->update($request->all());
        return redirect()->back()->with('success_update', 'Satuan berhasil diperbarui.');
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
