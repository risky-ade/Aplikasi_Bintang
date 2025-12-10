<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\LoginController;

use App\Http\Controllers\SatuanController;
use App\Http\Controllers\PemasokController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\MasterProdukController;
use App\Http\Controllers\ReturPembelianController;
use App\Http\Controllers\ReturPenjualanController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\LaporanPembelianController;
use App\Http\Controllers\LaporanPenjualanController;
use App\Http\Controllers\HistoriHargaPembelianController;
use App\Http\Controllers\HistoriHargaPenjualanController;
use App\Http\Controllers\UserController;

Route::middleware(['auth', 'role:superadmin'])->group(function () {
    // Manajemen Role & Permission
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('/roles/{role}/edit', [RoleController::class, 'editPermissions'])->name('roles.edit');
    Route::put('/roles/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('roles.update');

    // Manajemen User (assign role ke user)
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
});

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
Route::get('/master_produk/check-duplicate', [MasterProdukController::class, 'checkDuplicate'])->name('produk.check-duplicate')->middleware('auth');
Route::resource('/master_produk', MasterProdukController::class)->middleware('auth');



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
    Route::delete('/sales_retur/{id}', [ReturPenjualanController::class, 'destroy'])->name('retur-penjualan.destroy')->middleware('auth');
    Route::get('sales_retur/{id}', [ReturPenjualanController::class, 'show'])->name('retur-penjualan.show')->middleware('auth');
});
Route::get('/ajax/faktur-search', [ReturPenjualanController::class, 'searchFaktur'])->name('ajax.faktur-search')->middleware('auth');
    
Route::get('/sales/sales_histories', [HistoriHargaPenjualanController::class, 'index'])->middleware('auth')->name('histori-harga-jual.index');

Route::prefix('purchases')->group(function () {
    Route::get('purchase_inv', [PembelianController::class,'index'])->name('pembelian.index')->middleware('auth');
    Route::get('purchase_inv/create', [PembelianController::class,'create'])->name('pembelian.create')->middleware('auth');
    Route::post('purchase_inv/store', [PembelianController::class,'store'])->name('pembelian.store')->middleware('auth');
    Route::get('purchase_inv/{id}', [PembelianController::class,'show'])->name('pembelian.show')->middleware('auth');
    Route::get('purchase_inv/{id}/print', [PembelianController::class,'print'])->name('pembelian.print')->middleware('auth');
    Route::get('purchase_inv/{id}/edit', [PembelianController::class,'edit'])->name('pembelian.edit')->middleware('auth');
    Route::put('purchase_inv/{id}', [PembelianController::class,'update'])->name('pembelian.update')->middleware('auth');
    Route::put('purchase_inv/{id}/approve', [PembelianController::class,'approve'])->name('pembelian.approve')->middleware('auth');
    Route::put('purchase_inv/{id}/unapprove', [PembelianController::class,'unapprove'])->name('pembelian.unapprove')->middleware('auth');
    Route::put('purchase_inv/{id}/batal', [PembelianController::class,'batal'])->name('pembelian.batal')->middleware('auth');
    
    // Route::get('purchase_invoices/{id}/print', [PembelianController::class, 'print'])->name('purchase_invoices.print');
});
Route::prefix('purchases/purchases_retur')->middleware('auth')->group(function () {
    Route::get('/', [ReturPembelianController::class, 'index'])->name('retur-pembelian.index');
    Route::get('/create', [ReturPembelianController::class, 'create'])->name('retur-pembelian.create');
    Route::post('/', [ReturPembelianController::class, 'store'])->name('retur-pembelian.store');

    Route::get('/get-detail/{id}', [ReturPembelianController::class, 'getDetailPembelian'])
        ->name('ajax.pembelian-detail');

    Route::get('/search-faktur', [ReturPembelianController::class, 'searchFaktur'])
        ->name('ajax.pembelian-search');
    Route::get('/{id}', [ReturPembelianController::class, 'show'])->name('retur-pembelian.show');
    Route::delete('/{id}', [ReturPembelianController::class, 'destroy'])->name('retur-pembelian.destroy');
});

Route::get('/purchases/purchases_histories', [HistoriHargaPembelianController::class, 'index'])->middleware('auth')->name('histori-harga-beli.index');


Route::resource('categories', KategoriController::class)->except(['create','show','edit'])->middleware('auth');
// Route::get('/categories', [KategoriController::class, 'index'])->middleware('auth');
Route::post('/categories', [KategoriController::class, 'store'])->middleware('auth');
// Route::get('/categories/edit/{id}', [KategoriController::class, 'edit'])->name('categories.edit')->middleware('auth');
// Route::post('/categories/update', [KategoriController::class, 'update'])->name('categories.update')->middleware('auth');
Route::delete('/categories/{id}', [KategoriController::class, 'destroy'])->name('kategori.destroy')->middleware('auth');

Route::resource('/units',SatuanController::class)->middleware('auth');
// Route::post('/units', [SatuanController::class, 'store'])->name('satuan.store')->middleware('auth');
// Route::get('/units/edit/{id}', [SatuanController::class, 'edit'])->name('edit')->middleware('auth');
// Route::post('/units/update', [SatuanController::class, 'update'])->name('satuan.update')->middleware('auth');
// Route::delete('/units/{id}', [SatuanController::class, 'destroy'])->name('units.destroy')->middleware('auth');

Route::get('/reports/sales_report', [LaporanPenjualanController::class, 'index'])->name('sales_report.index')->middleware('auth');
Route::get('/reports/sales_pdf', [LaporanPenjualanController::class, 'pdf'])->name('sales_report.sales_pdf')->middleware('auth');

Route::get('/reports/purchases_pdf', [LaporanPembelianController::class, 'beliPdf'])->name('purchase_report.purchases_pdf')->middleware('auth');
Route::get('/reports/purchases_report', [LaporanPembelianController::class, 'index'])->name('purchases_report.index')->middleware('auth');

Route::resource('customers', PelangganController::class)->middleware('auth');

Route::resource('suppliers', PemasokController::class)->middleware('auth');
// Route::resource('users', UserManagementController::class)->middleware('auth');
Route::get('users',[UserManagementController::class, 'index'])->name('users.index')->middleware('auth');
Route::get('users/{id}/edit',[UserManagementController::class, 'edit'])->name('users.edit')->middleware('auth');
Route::put('users/{id}',[UserManagementController::class, 'update'])->name('users.update')->middleware('auth');

// Route::get('/users', function () {
//     return view('users.index');
// });
Route::get('/profile', function () {
    return view('profiles.profile');
});
