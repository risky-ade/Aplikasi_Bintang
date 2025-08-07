<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    const STATUS_AKTIF = 'aktif';
    const STATUS_BATAL = 'batal';
    protected $table = 'penjualan';
    protected $casts = ['approved_at'=>'datetime',];
    protected $fillable = [
        'no_faktur',
        'no_po',
        'no_surat_jalan',
        'tanggal',
        'pelanggan_id',
        'catatan',
        'pajak',
        'biaya_kirim',
        'total',
        'jatuh_tempo',
        'status_pembayaran',
        'approved_at',
        'status',
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

    public function returPenjualan()
    {
        return $this->hasMany(ReturPenjualan::class, 'penjualan_id');
    }
}
