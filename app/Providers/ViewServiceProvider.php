<?php

namespace App\Providers;

use App\Models\MasterProduk;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //View::composer('*', ...): akan dijalankan untuk semua view
        //produk_stok_minim otomatis tersedia di semua Blade view
        View::composer('*', function ($view) {
        $produk_stok_minim = MasterProduk::whereColumn('stok', '<=', 'stok_minimal')->get();
        $view->with('produk_stok_minim', $produk_stok_minim);
    });
    }
}
