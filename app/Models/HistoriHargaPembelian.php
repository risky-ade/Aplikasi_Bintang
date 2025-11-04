<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HistoriHargaPembelian extends Model
{
    use HasFactory;

    protected $table = 'histori_harga_pembelian';

    protected $fillable = [
        'produk_id',
        'pemasok_id',
        'sumber',
        'harga_lama',
        'harga_baru',
        'tanggal',
        'keterangan',
    ];
    protected $dates = ['tanggal'];

    // public function getTanggalFormattedAttribute()
    // {
    //     return optional($this->tanggal)->format('d-m-Y');
    // }

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


    public function pemasok()
    {
        return $this->belongsTo(Pemasok::class, 'pemasok_id');
    }
}
