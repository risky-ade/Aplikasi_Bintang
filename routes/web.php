<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
});
Route::get('/items', function(){
    return view('items');
});
Route::get('/categories', function(){
    return view('categories');
});
Route::get('/sales_order', function(){
    return view('sales.sales_order');
});
Route::get('/delivery_order', function(){
    return view('sales.delivery_order');
});
Route::get('/sales_invoice', function(){
    return view('sales.sales_invoice');
});
Route::get('/purchases_receive', function(){
    return view('purchases.purchases_receive');
});
Route::get('/purchases_invoice', function(){
    return view('purchases.purchases_invoice');
});
Route::get('/unit', function(){
    return view('unit');
});
Route::get('/sales_report', function(){
    return view('sales.sales_report');
});
Route::get('/purchases_report', function(){
    return view('purchases.purchases_report');
});
Route::get('/customers', function(){
    return view('customers');
});
Route::get('/suppliers', function(){
    return view('suppliers');
});
Route::get('/users', function(){
    return view('users');
});
Route::get('/profile', function(){
    return view('profile');
});