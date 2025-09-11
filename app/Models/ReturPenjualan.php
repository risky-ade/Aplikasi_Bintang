<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class ReturPenjualan extends Model
{
    protected $table = 'retur_penjualan';
    protected $fillable = [
        'no_retur','penjualan_id', 'tanggal_retur', 'alasan', 'total', 'created_by'
    ];

    protected $dates = ['tanggal_retur'];
    protected $casts = [
        'tanggal_retur' => 'date',
        
    ];

    protected function tanggal_retur(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => \Carbon\Carbon::parse($value)->format('d-m-Y'),
        );
    }
    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class);
    }

    public function details()
    {
        return $this->hasMany(ReturPenjualanDetail::class, 'retur_penjualan_id');
    }
}
