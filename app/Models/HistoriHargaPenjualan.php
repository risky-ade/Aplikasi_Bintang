<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
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
    protected $dates = ['tanggal'];

    protected function tanggal(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => \Carbon\Carbon::parse($value)->format('d-m-Y'),
        );
    }

   
    public function produk()
    {
        return $this->belongsTo(MasterProduk::class, 'produk_id');
    }


    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggan_id');
    }
}
