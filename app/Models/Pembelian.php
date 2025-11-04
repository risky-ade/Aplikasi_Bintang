<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Pembelian extends Model
{
    protected $table = 'pembelian';
    protected $fillable = [
        'no_faktur',
        'no_po',
        'tanggal',
        'pemasok_id',
        'catatan',
        'pajak',
        'biaya_kirim',
        'total',
        'jatuh_tempo',
        'status_pembayaran',
        'paid_date',
        'status',
        'approved_at',
        'approved_by',
        'created_by'
    ];
    protected $casts = [
        'tanggal' => 'date',
        'approved_at' => 'datetime',
        'paid_date'   => 'date',
    ];

    protected $dates = ['tanggal'];

    protected function tanggal(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => \Carbon\Carbon::parse($value)->format('d-m-Y'),
        );
    }

    public function pemasok()
    {
        return $this->belongsTo(Pemasok::class);
    }
    public function detail()
    {
        return $this->hasMany(PembelianDetail::class);
    }
}
