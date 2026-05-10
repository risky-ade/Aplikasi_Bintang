<?php

namespace App\Http\Controllers;

use App\Models\MasterProduk;
use App\Models\StokOpname;
use App\Models\StokOpnameDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StokOpnameController extends Controller
{
    public function index()
    {
        
        $data = StokOpname::latest()->get();
        return view('stock_opname.index', compact('data'));
    }

    public function create()
    {
        $produk = MasterProduk::where('is_active', true)->get();

        $no = 'SO-' . now()->format('YmdHi');

        return view('stock_opname.create', compact('produk','no'));
    }

    public function search(Request $request)
    {
        $term = $request->term;

        $produk = MasterProduk::where('is_active', true)
            ->where('nama_produk', 'LIKE', "%$term%")
            ->limit(10)
            ->get();

        $results = [];

        foreach ($produk as $item) {
            $results[] = [
                'id' => $item->id,
                'text' => $item->nama_produk,
                'stok' => $item->stok
            ];
        }

        return response()->json(['results' => $results]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_opname' => 'required|unique:stock_opnames,no_opname',
            'tanggal' => 'required|date',
            'produk_id' => 'required|array|min:1',
            'produk_id.*' => 'required|exists:master_produk,id|distinct',
            'stok_sistem' => 'required|array',
            'stok_sistem.*' => 'required|integer|min:0',
            'stok_fisik' => 'required|array',
            'stok_fisik.*' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();

        try {
            $opname = StokOpname::create([
                'no_opname' => $request->no_opname,
                'tanggal' => $request->tanggal,
                'status' => 'draft',
                'catatan' => $request->catatan,
                'created_by' => Auth::id(),
            ]);

            $details = [];

            foreach ($request->produk_id as $i => $pid) {
                $stokSistem = $request->stok_sistem[$i];
                $stokFisik  = $request->stok_fisik[$i] ?? 0;

                $selisih = $stokFisik - $stokSistem;

                $details[] = [
                    'stock_opname_id' => $opname->id,
                    'master_produk_id' => $pid,
                    'stok_sistem' => $stokSistem,
                    'stok_fisik' => $stokFisik,
                    'selisih' => $selisih,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            StokOpnameDetail::insert($details);

            DB::commit();

            return redirect()->route('stock_opname.index')
                ->with('success','Opname berhasil disimpan (draft)');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error',$e->getMessage());
        }
    }

    public function show($id)
    {
        $opname = StokOpname::with('details.produk')->findOrFail($id);

        return view('stock_opname.show', compact('opname'));
    }

    public function edit($id)
    {
        $opname = StokOpname::with('details.produk')->findOrFail($id);

        if ($opname->status === 'selesai') {
            return redirect()->route('stock_opname.index')
                ->with('error', 'Stock opname yang sudah selesai tidak dapat diedit.');
        }

        return view('stock_opname.edit', compact('opname'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'stok_fisik' => 'required|array|min:1',
            'stok_fisik.*' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();

        try {
            $opname = StokOpname::with('details')->findOrFail($id);

            if ($opname->status === 'selesai') {
                return redirect()->route('stock_opname.index')
                    ->with('error', 'Stock opname yang sudah selesai tidak dapat diedit.');
            }

            $opname->update([
                'tanggal' => $request->tanggal,
                'catatan' => $request->catatan,
            ]);

            foreach ($opname->details as $index => $detail) {
                $stokFisik = (int) ($request->stok_fisik[$index] ?? 0);

                $detail->update([
                    'stok_fisik' => $stokFisik,
                    'selisih' => $stokFisik - $detail->stok_sistem,
                ]);
            }

            DB::commit();

            return redirect()->route('stock_opname.index')
                ->with('success', 'Stock opname berhasil diupdate.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $opname = StokOpname::findOrFail($id);

        // if ($opname->status === 'selesai') {
        //     return back()->with('error', 'Stock opname yang sudah selesai tidak dapat dihapus.');
        // }

        $opname->delete();

        return redirect()->route('stock_opname.index')
            ->with('success', 'Stock opname berhasil dihapus.');
    }

    public function approve($id)
    {
        DB::beginTransaction();

        try {
            $opname = StokOpname::with('details')->findOrFail($id);

            if ($opname->status === 'selesai') {
                return back()->with('error','Sudah diselesaikan');
            }

            foreach ($opname->details as $d) {

                $produk = MasterProduk::lockForUpdate()->find($d->master_produk_id);

                if ($d->selisih > 0) {
                    $produk->increment('stok', $d->selisih);
                } elseif ($d->selisih < 0) {
                    $produk->decrement('stok', abs($d->selisih));
                }
            }

            $opname->update([
                'status' => 'selesai',
                'approved_by' => Auth::id(),
                'approved_at' => now()
            ]);

            DB::commit();
            Log::channel('stok')->info('Stok opname diselesaikan', [
                'opname_id' => $opname->id,
                'user' => Auth::user()->name
            ]);

            return back()->with('success','Stok berhasil disesuaikan');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error',$e->getMessage());
        }
    }
}
