<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\DashboardController;

Route::get('/', function () {
    return view('client.home');
});

Route::get('/admin', [AuthController::class, 'showloginform'])->name('login');
Route::post('/admin/postlogin', [AuthController::class, 'postlogin'])->name('postlogin');
Route::get('/admin/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});
