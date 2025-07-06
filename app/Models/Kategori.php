<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    protected $table = 'kategori';

    protected $fillable = ['kode_kategori', 'nama_kategori'];

    public function produk()
    {
        return $this->hasMany(MasterProduk::class, 'kategori_id');
    }

}
