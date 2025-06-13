<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MasterKategoriController;
use App\Http\Controllers\MasterProdukController;
use App\Http\Controllers\MasterSatuanController;
use App\Http\Controllers\ProdukController;
use App\Models\MasterKategori;
use App\Models\MasterSatuan;

Route::get('/login', function () {
    return view('login.index');
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

Route::resource('/master_produk', MasterProdukController::class)->middleware('auth');
// Route::get('/master_produk', [MasterProdukController::class,'index'])->middleware('auth');
// Route::get('/master_produk', [MasterProdukController::class,'create'])->middleware('auth');
// Route::get('/master_produk', [MasterProdukController::class,'edit'])->middleware('auth');

Route::resource('/products', ProdukController::class)->middleware('auth');
// Route::get('/products', [ProdukController::class, 'index'])->middleware('auth');
// Route::get('/products', [ProdukController::class, 'create'])->middleware('auth');
// Route::get('/edit-produk', [ProdukController::class, 'editProduk'])->name('editProduk');

Route::get('/categories', [MasterKategoriController::class, 'index'])->middleware('auth');
Route::get('/sales_invoice', function () {
    return view('sales.sales_invoices.index');
});
Route::get('/sales_retur', function () {
    return view('sales.sales_retur.index');
});
Route::get('/sales_histories', function () {
    return view('sales.sales_histories.index');
});
Route::get('/purchases_retur', function () {
    return view('purchases.purchases_retur.index');
});
Route::get('/purchases_invoice', function () {
    return view('purchases.purchases_invoices.index');
});
Route::get('/purchases_histories', function () {
    return view('purchases.purchases_histories.index');
});
Route::get('/units', [MasterSatuanController::class, 'index'])->middleware('auth');
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
