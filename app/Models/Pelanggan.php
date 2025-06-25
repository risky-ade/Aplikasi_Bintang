<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    protected $table = 'pelanggan';

    protected $fillable = [
        'nama',
        'email',
        'npwp',
        'no_hp',
        'kota',
        'provinsi',
        'alamat',
    ];
    public function penjualan()
    {
        return $this->hasMany(Penjualan::class);
    }
}
