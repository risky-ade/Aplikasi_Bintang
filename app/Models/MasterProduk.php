<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterProduk extends Model
{
    use HasFactory;
    public $timestamps = false;
    public $table = "master_produk";
    // protected $primaryKey = 'master_produk_id';
    protected $fillable = [
        'nama_produk',
        'deskripsi',
        'harga',
        'status',
        'update_at',
    ];

    // public function getMasterProduk()
    // {
    //     $query = MasterKategori::query()
    //         ->select('nama_produk', 'deskripsi', 'harga', 'master_produk_id')
    //         ->where('status', '=', '1')
    //         ->orderBy('master_produk_id', 'ASC')
    //         ->get();
    //     // dd($a);
    //     return $query;
    // }

    public function Produk()
    {
        return $this->hasMany(Produk::class);
    }
}
