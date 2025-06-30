<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    protected $table = 'penjualan';

    protected $fillable = [
        'no_faktur',
        'tanggal',
        'pelanggan_id',
        'catatan',
        'pajak',
        'biaya_kirim',
        'total',
        'jatuh_tempo',
        'created_by',
    ];

    public function detail()
    {
        return $this->hasMany(PenjualanDetail::class);
    }

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class);
    }
}
