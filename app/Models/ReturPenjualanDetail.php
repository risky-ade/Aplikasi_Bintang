<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturPenjualanDetail extends Model
{
    protected $table = 'retur_penjualan_detail';
    protected $fillable = [
        'retur_penjualan_id', 
        'penjualan_detail_id',
        'produk_id', 
        'qty_retur', 
        'harga_jual', 
        'harga_unit',          
        'diskon_unit',
        'subtotal'
    ];

    public function produk()
    {
        return $this->belongsTo(MasterProduk::class, 'produk_id');
    }

    public function retur()
    {
        return $this->belongsTo(ReturPenjualan::class, 'retur_penjualan_id');
    }
}
