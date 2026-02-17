<?php

namespace App\Http\Controllers;

use App\Models\Pemasok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PemasokController extends Controller
{
    public function index()
    {
        $pemasoks = Pemasok::latest()->paginate(10);
        return view('suppliers.index', compact('pemasoks'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        try{
            $validated = $request->validate([
                'nama' => 'required','string','max:255',
                'email'     => 'required','email','max:255','unique:pemasok,email',
                'npwp' => 'required','string','max:255',
                'no_hp' => 'required','string','max:255',
                'kota' => 'required','string','max:255',
                'provinsi' => 'required','string','max:255',
                'alamat' => 'required',
            ]);
    
            $pemasok = Pemasok::create($validated);
            Log::channel('pemasok')->info('Pemasok berhasil diperbarui', [
                    'pemasok_id' => $pemasok->id,
                    'nama' => $pemasok->nama,
                    'user'=>[
                            'id' => Auth::id(),
                            'name'=> Auth::user()->name??'-',
                            ],
                    'ip_address' => request()->ip(),
                    
            ]);
            return redirect()->route('suppliers.index')->with('success', 'Pemasok berhasil ditambahkan.');
        }catch(\Throwable $e){
            Log::channel('pemasok')->error('Pemasok gagal ditambahkan', [
                    'nama' => $request->nama ?? '',
                    'user'=>[
                            'id' => Auth::id(),
                            'name'=> Auth::user()->name,
                            ],
                    'ip_address' => request()->ip(),
                    'error' => $e->getMessage(),
            ]);
            return redirect()->route('suppliers.index')->with('success', 'Pemasok berhasil ditambahkan.');
        }

    }

    public function edit($id)
    {
        $pemasok = Pemasok::findOrFail($id);
        return view('suppliers.edit', compact('pemasok'));
    }

    public function update(Request $request, $id)
    {
        try{
            $pemasok = Pemasok::findOrFail($id);
            $request->validate([
                'nama' => 'required',
            ]);
    
            $pemasok->update($request->all());
            Log::channel('pemasok')->info('Pelanggan berhasil diperbarui', [
                    'pemasok_id' => $pemasok->id,
                    'nama' => $pemasok->nama,
                    'user'=>[
                            'id' => Auth::id(),
                            'name'=> Auth::user()->name,
                            ],
                    'ip_address' => request()->ip(),
                    
            ]);
            return redirect()->route('suppliers.index')->with('success', 'Pemasok berhasil diperbarui.');
            
        }catch(\Throwable $e){
            Log::channel('pemasok')->error('Pemasok gagal diperbarui', [
                    'pemasok_id' => $id,
                    'nama' => $request->nama ?? '',
                    'user'=>[
                            'id' => Auth::id(),
                            'name'=> Auth::user()->name,
                            ],
                    'ip_address' => request()->ip(),
                    'error' => $e->getMessage(),
            ]);
            return redirect()->route('suppliers.index')->with('error', 'Pemasok gagal diperbarui.');
        }
    }

    public function destroy(string $id)
    {
        try{
            $pemasok = Pemasok::findOrFail($id);
            //cek retur sebelum hapus
            // if ($pelanggan->penjualan && $pelanggan->penjualan->count() > 0) {
            //     return redirect()->route('customers.index')
            //         ->with('error', 'Pelanggan tidak dapat dihapus karena sudah dipakai pada transaksi.');
            // }
            if ($pemasok->pembelian()->exists()) {
                Log::channel('pemasok')->info('Pemasok tidak dapat dihapus - sudah digunakan dalam transaksi', [
                    'pelanggan_id' => $pemasok->id,
                    'nama' => $pemasok->nama,
                    'user'=>[
                            'id' => Auth::id(),
                            'name'=> Auth::user()->name,
                            ],
                    'ip_address' => request()->ip(),
                ]);
                return response()->json([
                    'message' => 'Pemasok tidak dapat dihapus karena sudah digunakan dalam transaksi.'
                ], 400);
            }
    
            $pemasok->delete();
            Log::channel('pemasok')->info('Pemasok dihapus', [
                'pelanggan_id' => $pemasok->id,
                'nama' => $pemasok->nama,
                'user'=>[
                        'id' => Auth::id(),
                        'name'=> Auth::user()->name,
                        ],
                'ip_address' => request()->ip(),
             
            ]);
            return response()->json(['message'=>'Pemasok berhasil dihapus.']);

        }catch(\Throwable $e){
            Log::channel('pemasok')->error('Pemasok gagal dihapus', [
                'pemasok_id' => $id,
                'user'=>[
                        'id' => Auth::id(),
                        'name'=> Auth::user()->name,
                        ],
                'ip_address' => request()->ip(),
                'error' => $e->getMessage(),
            ]);
            return response()->json(['message'=>'Pemasok gagal dihapus.'],500);
        }
        // $pelanggan->delete();
        // return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil dihapus.');
    }
}
