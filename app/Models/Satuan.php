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

        public function produk()
    {
        return $this->hasMany(MasterProduk::class, 'satuan_id');
    }


}
