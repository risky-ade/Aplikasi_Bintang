<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pemasok extends Model
{
    protected $table = 'pemasok';

    protected $fillable = [
        'nama',
        'email',
        'npwp',
        'no_hp',
        'kota',
        'provinsi',
        'alamat',
    ];
    public function pembelian()
    {
        return $this->hasMany(Pembelian::class);
    }
}
