<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterKategori extends Model
{
    public $timestamps = false;
    public $table = "master_kategori";
    protected $primaryKey = 'id_master_kategori';
    protected $fillable = [
        'kode_kategori',
        'nama_kategori',
        'update_at',
    ];


    public function getKategori()
    {
        $query = MasterKategori::query()
            ->select('kode_kategori', 'nama_kategori', 'id_master_kategori')
            ->orderBy('id_master_kategori', 'ASC')
            ->get();
        // dd($a);
        return $query;
    }
}
