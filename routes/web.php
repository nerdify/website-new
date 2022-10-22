<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::post('/process', [App\Http\Controllers\HomeController::class, 'process'])->name('process');

Route::get('/invoices', [App\Http\Controllers\HomeController::class, 'invoiceList'])->name('invoices');
Route::get('/invoices/{invoice}', [App\Http\Controllers\HomeController::class, 'invoice'])->name('invoice');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
