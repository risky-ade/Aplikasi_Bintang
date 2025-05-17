<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterProduk extends Model
{
    public $timestamps = false;
    public $table = "master_produk";
    protected $primaryKey = 'id_master_produk';
    protected $fillable = [
        'nama_produk',
        'deskripsi',
        'harga',
        'status',
        'update_at',
    ];

    public function getMasterProduk()
    {
        $query = MasterKategori::query()
            ->select('nama_produk', 'deskripsi', 'harga', 'id_master_produk')
            ->where('status', '=', '1')
            ->orderBy('id_master_produk', 'ASC')
            ->get();
        // dd($a);
        return $query;
    }
}
