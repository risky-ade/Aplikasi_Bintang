<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PelangganController extends Controller
{
    public function index()
    {
        $pelanggans = Pelanggan::latest()->paginate(10);
        return view('customers.index', compact('pelanggans'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        try{
            $validated = $request->validate([
                'nama' => 'required',
                'email'     => 'required','email','max:255','unique:pelanggan,email',
                'npwp' => 'required','string','max:255',
                'no_hp' => 'required','string','max:255',
                'kota' => 'required','string','max:255',
                'provinsi' => 'required','string','max:255',
                'alamat' => 'required',
            ]);

            $pelanggan = Pelanggan::create($validated);
            Log::channel('pelanggan')->info('Pelanggan berhasil diperbarui', [
                    'pelanggan_id' => $pelanggan->id,
                    'nama' => $pelanggan->nama,
                    'user'=>[
                            'id' => Auth::id(),
                            'name'=> Auth::user()->name ??'-',
                            ],
                    'ip_address' => request()->ip(),
                    
            ]);
            return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil ditambahkan.');
        }catch(\Throwable $e){
        Log::channel('pelanggan')->error('Pelanggan gagal ditambahkan', [
                    'nama' => $request->nama ?? '',
                    'user'=>[
                            'id' => Auth::id(),
                            'name'=> Auth::user()->name,
                            ],
                    'ip_address' => request()->ip(),
                    'error' => $e->getMessage(),
            ]);
            return redirect()->route('customers.index')->with('error', 'Pelanggan gagal ditambahkan.');
        }
    }

    public function edit($id)
    {
        $pelanggan = Pelanggan::findOrFail($id);
        return view('customers.edit', compact('pelanggan'));
    }

    public function update(Request $request,$id)
    {
        try{
            $pelanggan = Pelanggan::findOrFail($id);
            $request->validate([
                'nama' => 'required',
                'email'     => 'required','email','max:255','unique:pelanggan,email',
                'npwp' => 'required','string','max:255',
                'no_hp' => 'required','string','max:255',
                'kota' => 'required','string','max:255',
                'provinsi' => 'required','string','max:255',
                'alamat' => 'required',
            ]);
    
            $pelanggan->update($request->all());
            Log::channel('pelanggan')->info('Pelanggan berhasil diperbarui', [
                    'pelanggan_id' => $pelanggan->id,
                    'nama' => $pelanggan->nama,
                    'user'=>[
                            'id' => Auth::id(),
                            'name'=> Auth::user()->name,
                            ],
                    'ip_address' => request()->ip(),
                    
            ]);
            return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil diperbarui.');

        }catch(\Throwable $e){
            Log::channel('pelanggan')->error('Pelanggan gagal diperbarui', [
                    'pelanggan_id' => $id,
                    'nama' => $request->nama ?? '',
                    'user'=>[
                            'id' => Auth::id(),
                            'name'=> Auth::user()->name,
                            ],
                    'ip_address' => request()->ip(),
                    'error' => $e->getMessage(),
            ]);
            return redirect()->route('customers.index')->with('error', 'Pelanggan gagal diperbarui.');
        }
    }

    public function destroy(string $id)
    {
        try{
            $pelanggan = Pelanggan::findOrFail($id);

                if ($pelanggan->penjualan()->exists()) {
                    Log::channel('pelanggan')->info('Pelanggan tidak dapat dihapus - sudah digunakan dalam transaksi', [
                    'pelanggan_id' => $pelanggan->id,
                    'nama' => $pelanggan->nama,
                    'user'=>[
                            'id' => Auth::id(),
                            'name'=> Auth::user()->name,
                            ],
                    'ip_address' => request()->ip(),
                ]);

                    return response()->json([
                        'message' => 'Pelanggan tidak dapat dihapus karena sudah digunakan dalam transaksi.'
                    ], 400);
                }
        
                $pelanggan->delete();

                Log::channel('pelanggan')->info('Pelanggan dihapus', [
                    'pelanggan_id' => $pelanggan->id,
                    'nama' => $pelanggan->nama,
                    'user'=>[
                            'id' => Auth::id(),
                            'name'=> Auth::user()->name,
                            ],
                    'ip_address' => request()->ip(),
                ]);
                return response()->json(['message'=>'Pelanggan berhasil dihapus.']);

        }catch(\Throwable $e){

        Log::channel('pelanggan')->error('Pelanggan gagal dihapus', [
                'pelanggan_id' => $id,
                'user'=>[
                        'id' => Auth::id(),
                        'name'=> Auth::user()->name,
                        ],
                'ip_address' => request()->ip(),
                'error' => $e->getMessage(),
            ]);
            return response()->json(['message'=>'Pelanggan gagal dihapus.'],500);

        }

    }
}
