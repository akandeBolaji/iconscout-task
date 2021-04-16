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

Route::middleware(['auth'])->group(function () {
    Route::get('/admin', [App\Http\Controllers\AdminController::class, 'index'])->name('dashboard');
    Route::post('/admin/logout', [App\Http\Controllers\AdminController::class, 'logout'])->name('logout');
    Route::get('/admin/icons ', [App\Http\Controllers\AdminController::class, 'icon'])->name('icon');
    Route::resource('/admin/members', App\Http\Controllers\MemberController::class);
});

Route::middleware(['guest'])->group(function () {
    Route::get('/admin/login', [App\Http\Controllers\AdminController::class, 'login'])->name('login');
    Route::post('/admin/login', [App\Http\Controllers\AdminController::class, 'authenticate'])->name('authenticate');
});
