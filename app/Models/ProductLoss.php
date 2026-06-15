<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductLoss extends Model
{
    protected $fillable = [
        'tanggal',
        'master_produk_id',
        'qty',
        'keterangan',
        'created_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'qty' => 'integer',
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
