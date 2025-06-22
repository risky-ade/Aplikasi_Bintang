<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterSatuan extends Model
{
    // public $timestamps = false;
    public $table = "master_satuan";
    protected $primaryKey = 'id';
    protected $fillable = [
        'jenis_satuan',
        'keterangan_satuan',
        'update_at',
    ];

        public function MasterProduk()
    {
        return $this->hasMany(MasterProduk::class, 'master_kategori_id','id');
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
