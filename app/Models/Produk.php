<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
// use function Laravel\Prompts\select;
use Illuminate\Support\Facades\DB;

class Produk extends Model
{
    use HasFactory;
    // protected $with=['master_produk','master_kategori','master_satuan'];
    public $timestamps = false;
    public $table = "produk";
    protected $guarded=[];
    protected $fillable = [
        'master_produk_id',
        'master_kategori_id',
        'master_satuan_id',
        'stok',
    ];

    
    public function masterProduk()
    {
        return $this->belongsTo(MasterProduk::class, 'master_produk_id','id');
    }

    public function masterKategori()
    {
        return $this->belongsTo(MasterKategori::class,'master_kategori_id','id');
    }

    public function masterSatuan()
    {
        return $this->belongsTo(MasterSatuan::class,'master_satuan_id','id');
    }

    // public function getProduk()
    // {
    //     $query = "select p.produk_id,mp.nama_produk, mp.harga, mp.deskripsi, mk.nama_kategori, ms.jenis_satuan, p.stok from produk p 
    //             inner join master_produk mp on mp.master_produk_id = p.id_master_produk
    //             inner join master_kategori mk on mk.master_kategori_id = p.master_kategori_id 
    //             inner join master_satuan ms on ms.master_satuan_id = p.master_satuan_id ";
    //     return DB::select($query);
    // }

}
