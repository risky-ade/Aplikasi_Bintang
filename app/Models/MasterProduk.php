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
        'kategori_id',
        'satuan_id',
        'harga_dasar',
        'harga_jual',
        'include_pajak',
        'stok',
        'stok_minimal',
        'gambar',
        'update_at',
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function satuan()
    {
        return $this->belongsTo(Satuan::class, 'satuan_id');
    }
        public function penjualanDetail()
    {
        return $this->hasMany(PenjualanDetail::class, 'master_produk_id');
    }

    public function returPenjualanDetail()
    {
        return $this->hasMany(ReturPenjualanDetail::class, 'produk_id');
    }
}
