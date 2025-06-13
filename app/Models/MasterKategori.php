<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterKategori extends Model
{
    use HasFactory;
    // public $timestamps = false;
    // public $table = "master_kategori";
    // protected $primaryKey = 'id_master_kategori';
    // protected $fillable = [
    //     'kode_kategori',
    //     'nama_kategori',
    //     'update_at',
    // ];

    protected $table = "master_kategori";
    protected $guarded = ["id"];
    protected $fillable = ['kode_kategori','nama_kategori','update_at'];


    public function getKategori()
    {
        $query = MasterKategori::query()
            ->select('kode_kategori', 'nama_kategori', 'master_kategori_id')
            ->orderBy('master_kategori_id', 'ASC')
            ->get();
        // dd($a);
        return $query;
    }
}
