<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProdukController;

Route::get('/login', function () {
    return view('login.index');
});

Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate']);
Route::post('/logout', [LoginController::class, 'logout']);

Route::get('/', function () {
    return view('dashboard');
});
Route::get('/dashboard', function () {
    return view('dashboard');
});
Route::get('/master_produk', function () {
    return view('master_produk.master_produk');
});
Route::get('/items', [ProdukController::class, 'getProduk'])->name('getProduk');
Route::get('/tambah-produk', [ProdukController::class, 'tambahProduk'])->name('tambahProduk');
Route::get('/edit-produk', [ProdukController::class, 'editProduk'])->name('editProduk');

Route::get('/categories', function () {
    return view('categories.index');
});
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
Route::get('/unit', function () {
    return view('units.unit');
});
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
