<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    protected $fillable = [
        'master_produk_id',
        'tanggal',
        'deskripsi',
        'qty_masuk',
        'qty_keluar',
        'sisa',
        'reference_type',
        'reference_id',
        'created_by',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'qty_masuk' => 'integer',
        'qty_keluar' => 'integer',
        'sisa' => 'integer',
    ];

    public function produk(): BelongsTo
    {
        return $this->belongsTo(MasterProduk::class, 'master_produk_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
