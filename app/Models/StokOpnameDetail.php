<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StokOpnameDetail extends Model
{
    protected $table= 'stock_opname_details';
    protected $fillable = [
        'stock_opname_id',
        'master_produk_id',
        'stok_sistem',
        'stok_fisik',
        'selisih'
    ];

    public function produk()
    {
        return $this->belongsTo(MasterProduk::class, 'master_produk_id');
    }
}
