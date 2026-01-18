<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfilePerusahaan extends Model
{
    protected $fillable = [
        'nama_perusahaan',
        'email',
        'telepon',
        'alamat',
        'nama_bank',
        'no_rekening',
    ];
}
