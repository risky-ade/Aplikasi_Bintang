<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MasterProduk extends Model
{
    use HasFactory;
    // protected $with = ['master_kategori'];
    public $timestamps = false;
    public $table = "master_produk";
    // protected $primaryKey = 'master_produk_id';
    protected $fillable = [
        'nama_produk',
        'deskripsi',
        'master_kategori_id',
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

    // public function Produk()
    // {
    //     return $this->hasOne(Produk::class);
    // }
    public function masterKategori()
    {
        return $this->belongsTo(MasterKategori::class, 'master_kategori_id');
    }
}
