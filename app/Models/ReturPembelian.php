<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class ReturPembelian extends Model
{
    protected $table = 'retur_pembelian';

    protected $fillable = [
        'no_retur',
        'pembelian_id',
        'tanggal_retur',
        'alasan',
        'total',
        'created_by',
    ];

    protected $casts = [
        'tanggal_retur' => 'date',
    ];

    protected function tanggal_retur(): Attribute
    {
        return Attribute::make(
            get: fn($value) => \Carbon\Carbon::parse($value)->format('d-m-Y'),
        );
    }

    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class);
    }

    public function details()
    {
        return $this->hasMany(ReturPembelianDetail::class, 'retur_pembelian_id');
    }
}
