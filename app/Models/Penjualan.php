<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Penjualan extends Model
{
    const STATUS_AKTIF = 'aktif';
    const STATUS_BATAL = 'batal';
    protected $table = 'penjualan';
    protected $casts = ['approved_at'=>'datetime','jatuh_tempo' => 'date','paid_date'   => 'date',];
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
        'total_netto_calc',
        'jatuh_tempo',
        'status_pembayaran',
        'paid_date',
        'approved_at',
        'approved_by',
        'status',
        'created_by',
    ];
    protected $dates = ['tanggal','jatuh_tempo'];

    protected function tanggal(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => \Carbon\Carbon::parse($value)->format('d-m-Y'),
        );
    }
    protected function jatuh_tempo(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => \Carbon\Carbon::parse($value)->format('d-m-Y'),
        );
    }

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

    public function returDetails() // detail lewat header retur
    {
        return $this->hasManyThrough(
            ReturPenjualanDetail::class,   // model tujuan
            ReturPenjualan::class,         // model perantara
            'penjualan_id',                // FK di tabel retur -> ke penjualan
            'retur_penjualan_id',                    // FK di tabel detail -> ke retur
            'id',                          // PK penjualan
            'id'                           // PK retur
        );
    }
}
