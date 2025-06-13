<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// use function Laravel\Prompts\select;
use Illuminate\Support\Facades\DB;

class Produk extends Model
{
    use HasFactory;
    // protected $with=['master_produk','master_kategori','master_satuan'];
    public $timestamps = false;
    public $table = "produk";
    protected $fillable = [
        'master_produk_id',
        'master_kategori_id',
        'master_satuan_id',
        'stok',
    ];
    // public function Produk()
    // {
    //     return $this->hasMany(Produk::class);
    // }
    public function masterProduk()
    {
        return $this->belongsTo(MasterProduk::class,'master_produk_id');
    }

    public function masterKategori()
    {
        return $this->belongsTo(MasterKategori::class,);
    }

    public function masterSatuan()
    {
        return $this->belongsTo(MasterSatuan::class);
    }

    // public function getProduk()
    // {
    //     $query = "select p.id_produk,mp.nama_produk, mp.harga, mp.deskripsi, mk.nama_kategori, ms.jenis_satuan, p.stok from produk p 
    //             inner join master_produk mp on mp.id_master_produk = p.id_master_produk
    //             inner join master_kategori mk on mk.id_master_kategori = p.id_master_kategori 
    //             inner join master_satuan ms on ms.id_master_satuan = p.id_master_satuan ";
    //     return DB::select($query);
    // }
}
