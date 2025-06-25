<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterKategori extends Model
{
    use HasFactory;

    protected $table = "master_kategori";
    // protected $guarded = ["id"];
    protected $fillable = ['kode_kategori','nama_kategori','update_at'];


    public function masterProduk()
    {
        return $this->hasMany(MasterProduk::class, 'master_kategori_id');
    }
    public function Produk()
    {
        return $this->hasMany(Produk::class);
    }

}
