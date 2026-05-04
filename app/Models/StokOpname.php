<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StokOpname extends Model
{
    protected $table= 'stock_opnames';
    protected $fillable = [
        'no_opname',
        'tanggal',
        'status',
        'catatan',
        'created_by',
        'approved_by',
        'approved_at'
    ];

    public function details()
    {
        return $this->hasMany(StokOpnameDetail::class, 'stock_opname_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
