<?php

namespace App\Support;

use App\Models\MasterProduk;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockMovementService
{
    public static function record(
        int $produkId,
        string $tanggal,
        string $deskripsi,
        int $masuk = 0,
        int $keluar = 0,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?string $keterangan = null,
        ?int $createdBy = null
    ): StockMovement {
        $masuk = max(0, $masuk);
        $keluar = max(0, $keluar);

        return DB::transaction(function () use ($produkId, $tanggal, $deskripsi, $masuk, $keluar, $referenceType, $referenceId, $keterangan, $createdBy) {
            MasterProduk::lockForUpdate()->findOrFail($produkId);

            $movement = StockMovement::create([
                'master_produk_id' => $produkId,
                'tanggal' => $tanggal,
                'deskripsi' => $deskripsi,
                'qty_masuk' => $masuk,
                'qty_keluar' => $keluar,
                'sisa' => 0,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'created_by' => $createdBy ?? Auth::id(),
                'keterangan' => $keterangan,
            ]);

            self::recalculateProduct($produkId);

            return $movement->fresh();
        });
    }

    public static function syncProductStock(int $produkId): int
    {
        return DB::transaction(fn () => self::recalculateProduct($produkId));
    }

    private static function recalculateProduct(int $produkId): int
    {
        $sisa = 0;

        $movements = StockMovement::where('master_produk_id', $produkId)
            ->lockForUpdate()
            ->orderBy('tanggal')
            ->orderBy('id')
            ->get();

        foreach ($movements as $movement) {
            $sisa += (int) $movement->qty_masuk - (int) $movement->qty_keluar;

            if ($sisa < 0) {
                abort(422, 'Stok produk tidak mencukupi untuk mutasi keluar.');
            }

            if ((int) $movement->sisa !== $sisa) {
                $movement->update(['sisa' => $sisa]);
            }
        }

        MasterProduk::where('id', $produkId)->update(['stok' => $sisa]);

        return $sisa;
    }
}
