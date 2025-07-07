<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenjualanDetail extends Model
{
    protected $table = 'penjualan_detail';

    protected $fillable = [
        'penjualan_id',
        'master_produk_id',
        'qty',
        'harga_jual',
        'subtotal'
    ];
    public function produk()
    {
        return $this->belongsTo(MasterProduk::class, 'master_produk_id');
    }
    
}
