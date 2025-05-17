<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// use function Laravel\Prompts\select;
use Illuminate\Support\Facades\DB;

class Produk extends Model
{
    public $timestamps = false;
    public $table = "master_produk";
    protected $fillable = [
        'id_master_produk',
        'id_master_kategori',
        'id_master_satuan',
        'stok',
    ];

    public function getProduk()
    {
        $query = "select p.id_produk,mp.nama_produk, mp.harga, mp.deskripsi, mk.nama_kategori, ms.jenis_satuan, p.stok from produk p 
                inner join master_produk mp on mp.id_master_produk = p.id_master_produk
                inner join master_kategori mk on mk.id_master_kategori = p.id_master_kategori 
                inner join master_satuan ms on ms.id_master_satuan = p.id_master_satuan ";
        return DB::select($query);
    }
}
