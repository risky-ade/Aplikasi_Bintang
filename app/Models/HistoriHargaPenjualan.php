<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HistoriHargaPenjualan extends Model
{
    use HasFactory;

    protected $table = 'histori_harga_penjualan';

    protected $fillable = [
        'produk_id',
        'pelanggan_id',
        'sumber',
        'harga_lama',
        'harga_baru',
        'tanggal',
        'keterangan',
    ];

    // Relasi ke produk
    public function produk()
    {
        return $this->belongsTo(MasterProduk::class, 'produk_id');
    }

    // Relasi ke pelanggan 
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggan_id');
    }
}
