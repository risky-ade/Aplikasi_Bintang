<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
});
Route::get('/items', function(){
    return view('items.items');
});
Route::get('/categories', function(){
    return view('categories.categories');
});
Route::get('/sales_invoice', function(){
    return view('sales.sales_invoices.index');
});
Route::get('/sales_retur', function(){
    return view('sales.sales_retur.index');
});
Route::get('/purchases_retur', function(){
    return view('purchases.purchases_retur.index');
});
Route::get('/purchases_invoice', function(){
    return view('purchases.purchases_invoices.index');
});
Route::get('/unit', function(){
    return view('units.unit');
});
Route::get('/sales_report', function(){
    return view('reports.sales_report');
});
Route::get('/purchases_report', function(){
    return view('reports.purchases_report');
});
Route::get('/customers', function(){
    return view('customers.customers');
});
Route::get('/suppliers', function(){
    return view('suppliers.suppliers');
});
Route::get('/users', function(){
    return view('users.users');
});
Route::get('/profile', function(){
    return view('profiles.profile');
});