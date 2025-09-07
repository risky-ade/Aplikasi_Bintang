<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembelianDetail extends Model
{
    protected $table = 'pembelian_detail';
    protected $fillable = [
        'pembelian_id',
        'master_produk_id',
        'qty',
        'harga_beli',
        'diskon',
        'subtotal'
    ];

    // public function pembelian()
    // {
    //     return $this->belongsTo(Pembelian::class);
    // }
    public function produk()
    {
        return $this->belongsTo(MasterProduk::class, 'master_produk_id');
    }
}
