<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturPembelianDetail extends Model
{
    protected $table = 'retur_pembelian_detail';

    protected $fillable = [
        'retur_pembelian_id',
        'produk_id',
        'qty_retur',
        'harga_beli',
        'diskon_unit',
        'subtotal',
    ];

    public function produk()
    {
        return $this->belongsTo(MasterProduk::class, 'produk_id');
    }

    public function retur()
    {
        return $this->belongsTo(ReturPembelian::class, 'retur_pembelian_id');
    }
}
