<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturPenjualan extends Model
{
    protected $table = 'retur_penjualan';
    protected $fillable = [
        'no_retur','penjualan_id', 'tanggal_retur', 'alasan', 'total', 'created_by'
    ];

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class);
    }

    public function details()
    {
        return $this->hasMany(ReturPenjualanDetail::class, 'retur_penjualan_id');
    }
}
