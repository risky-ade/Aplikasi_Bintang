<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterSatuan extends Model
{
    public $timestamps = false;
    public $table = "master_satuan";
    protected $primaryKey = 'id_master_satuan';
    protected $fillable = [
        'jenis_satuan',
        'keterangan_satuan',
        'update_at',
    ];

    public function getSatuan()
    {
        $query = MasterKategori::query()
            ->select('jenis_satuan', 'keterangan_satuan', 'id_master_satuan')
            ->orderBy('id_master_kategori', 'ASC')
            ->get();
        // dd($a);
        return $query;
    }
}
