<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\SatuanController;

use App\Http\Controllers\KategoriController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\MasterProdukController;
use App\Http\Controllers\ReturPenjualanController;
use App\Http\Controllers\HistoriHargaPenjualanController;


// Route::get('/login', function () {
//     return view('login.index');
// });

Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate']);
Route::post('/logout', [LoginController::class, 'logout']);

Route::get('/', function () {
    return view('dashboard');
})->middleware('auth');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth');

Route::get('/master_produk/search', [MasterProdukController::class, 'search'])->name('produk.search')->middleware('auth');
Route::resource('/master_produk', MasterProdukController::class)->middleware('auth');
// Route::get('/master_produk', [MasterProdukController::class,'index'])->middleware('auth');
// Route::get('/master_produk', [MasterProdukController::class,'create'])->middleware('auth');
// Route::get('/master_produk', [MasterProdukController::class,'edit'])->middleware('auth');

// Route::resource('/products', ProdukController::class)->middleware('auth');
// Route::get('/products', [ProdukController::class, 'getProduk'])->name('getProduk')->middleware('auth');
// Route::get('/products', [ProdukController::class, 'create'])->middleware('auth');
// Route::put('/edit-produk', [ProdukController::class, 'update'])->middleware('auth');



// Route::get('/sales_invoice', function () {
//     // return view('sales.sales_invoices.index');
// });


Route::get('/sales/sales_invoices/{id}/surat-jalan', [PenjualanController::class, 'suratJalan'])->name('sales.sales_invoices.surat-jalan')->middleware('auth');
Route::get('/sales/sales_invoices/{id}/print-surat-jalan', [PenjualanController::class, 'printSuratJalan'])->name('penjualan.print-surat-jalan')->middleware('auth');
Route::get('/sales/sales_invoices/{id}/surat-jalan-pdf', [PenjualanController::class, 'suratJalanPdf'])->name('penjualan.surat-jalan-pdf')->middleware('auth');
    // Route::resource('sales_invoices', PenjualanController::class)->middleware('auth');
Route::get('/sales/sales_invoices', [PenjualanController::class, 'index'])->name('penjualan.index')->middleware('auth');
Route::get('/sales/sales_invoices/create', [PenjualanController::class, 'create'])->name('penjualan.create')->middleware('auth');
Route::post('/sales/sales_invoices', [PenjualanController::class, 'store'])->name('penjualan.store')->middleware('auth');
Route::get('sales/sales_invoices/{id}/edit', [PenjualanController::class, 'edit'])->name('penjualan.edit');
Route::put('/sales/sales_invoices/{id}', [PenjualanController::class, 'update'])->name('penjualan.update');
Route::get('sales/sales_invoices/{id}', [PenjualanController::class, 'show'])->name('penjualan.show')->middleware('auth');
Route::get('sales_invoices/{id}/print', [PenjualanController::class, 'print'])->name('penjualan.print')->middleware('auth');
Route::delete('sales/sales_invoices/{id}', [PenjualanController::class, 'destroy'])->name('penjualan.destroy')->middleware('auth');
Route::get('sales/sales_invoices/{id}/print-pdf', [PenjualanController::class, 'printPdf'])->name('penjualan.print-pdf')->middleware('auth');
Route::put('sales/sales_invoices/{id}/approve', [PenjualanController::class, 'approve'])->name('penjualan.approve')->middleware('auth');
Route::put('sales/sales_invoices/{id}/unapprove', [PenjualanController::class, 'unapprove'])->name('penjualan.unapprove')->middleware('auth');
Route::put('sales/sales_invoices/{id}/batal', [PenjualanController::class, 'batal'])->name('penjualan.batal')->middleware('auth');
// Route::post('sales/sales_invoices/{id}/cancel-approve', [PenjualanController::class, 'cancelApprove'])->name('penjualan.cancelApprove')->middleware('auth');
// Route::patch('/sales/sales_invoices/{id}/approve', [PenjualanController::class, 'approve'])->name('penjualan.approve');
Route::prefix('sales')->group(function () {
    Route::get('sales_retur', [ReturPenjualanController::class, 'index'])->name('retur-penjualan.index')->middleware('auth');
    Route::get('sales_retur/create', [ReturPenjualanController::class, 'create'])->name('retur-penjualan.create')->middleware('auth');
    Route::post('sales_retur/store', [ReturPenjualanController::class, 'store'])->name('retur-penjualan.store')->middleware('auth');
    Route::get('sales_retur/get-detail/{id}', [ReturPenjualanController::class, 'getDetailPenjualan'])->name('retur-penjualan.get-detail')->middleware('auth');
    Route::delete('sales_retur/{id}', [ReturPenjualanController::class, 'destroy'])->name('retur-penjualan.destroy')->middleware('auth');
    Route::get('sales_retur/{id}', [ReturPenjualanController::class, 'show'])->name('retur-penjualan.show')->middleware('auth');
});
    
Route::get('/sales/sales_histories', [HistoriHargaPenjualanController::class, 'index'])->middleware('auth')->name('histori-harga.index');

// Route::get('/sales_retur', function () {
//     return view('sales.sales_retur.index');
// });
// Route::get('/sales_histories', function () {
//     return view('sales.sales_histories.index');
// });
Route::get('/purchases_retur', function () {
    return view('purchases.purchases_retur.index');
});
Route::get('/purchases_invoice', function () {
    return view('purchases.purchases_invoices.index');
});
Route::get('/purchases_histories', function () {
    return view('purchases.purchases_histories.index');
});
Route::resource('categories', KategoriController::class)->except(['create','show','edit'])->middleware('auth');
// Route::get('/categories', [KategoriController::class, 'index'])->middleware('auth');
Route::post('/categories', [KategoriController::class, 'store'])->middleware('auth');
// Route::get('/categories/edit/{id}', [KategoriController::class, 'edit'])->name('categories.edit')->middleware('auth');
// Route::post('/categories/update', [KategoriController::class, 'update'])->name('categories.update')->middleware('auth');
Route::delete('/categories/{id}', [KategoriController::class, 'destroy'])->name('kategori.destroy')->middleware('auth');

// Route::get('/units', [SatuanController::class, 'index'])->middleware('auth');
Route::resource('/units',SatuanController::class)->middleware('auth');
// Route::post('/units', [SatuanController::class, 'store'])->name('satuan.store')->middleware('auth');
// Route::get('/units/edit/{id}', [SatuanController::class, 'edit'])->name('edit')->middleware('auth');
// Route::post('/units/update', [SatuanController::class, 'update'])->name('satuan.update')->middleware('auth');
// Route::delete('/units/{id}', [SatuanController::class, 'destroy'])->name('units.destroy')->middleware('auth');


Route::get('/sales_report', function () {
    return view('reports.sales_report');
});
Route::get('/purchases_report', function () {
    return view('reports.purchases_report');
});
Route::get('/customers', function () {
    return view('customers.index');
});
Route::get('/suppliers', function () {
    return view('suppliers.suppliers');
});
Route::get('/users', function () {
    return view('users.index');
});
Route::get('/profile', function () {
    return view('profiles.profile');
});
