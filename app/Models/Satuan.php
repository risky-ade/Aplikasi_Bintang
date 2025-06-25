<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Satuan extends Model
{
    // public $timestamps = false;
    public $table = "satuan";
    protected $primaryKey = 'id';
    protected $fillable = [
        'jenis_satuan',
        'keterangan_satuan',
        'update_at',
    ];

        public function masterProduk()
    {
        return $this->hasMany(MasterProduk::class, 'master_satuan_id');
    }
    public function Produk()
    {
        return $this->hasMany(Produk::class);
    }
    // public function getSatuan()
    // {
    //     $query = MasterKategori::query()
    //         ->select('jenis_satuan', 'keterangan_satuan', 'master_satuan_id')
    //         ->orderBy('master_kategori_id', 'ASC')
    //         ->get();
    //     // dd($a);
    //     return $query;
    // }
}
