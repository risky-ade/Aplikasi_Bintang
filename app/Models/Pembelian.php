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
        'diskon_nota',
        'total',
        'total_netto_calc',
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

    public function returPembelian()
    {
        return $this->hasMany(ReturPembelian::class, 'pembelian_id');
    }

    public function returDetails() // detail lewat header retur
    {
        return $this->hasManyThrough(
            ReturPembelianDetail::class,   // model tujuan
            ReturPembelian::class,         // model perantara
            'pembelian_id',                // FK di tabel retur -> ke pembelian
            'retur_pembelian_id',          // FK di tabel detail -> ke retur
            'id',                          // PK pembelian
            'id'                           // PK retur
        );
    }
}
