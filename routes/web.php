<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DoctorController;
use App\Http\Controllers\Admin\DoctorEducationController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\SpecialityController;

Route::name('client.')->group(function () {
    include 'client.php';
});


Route::get('/admin', [AuthController::class, 'showloginform'])->name('login');
Route::post('/admin/postlogin', [AuthController::class, 'postlogin'])->name('postlogin');
Route::get('/admin/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('speciality', SpecialityController::class);
    Route::resource('doctor', DoctorController::class);

    Route::get('doctor-education/list/{id}', [DoctorEducationController::class, 'list'])->name('doctor-education.list');
    Route::resource('doctor-education', DoctorEducationController::class);

    Route::resource('services', ServiceController::class);
});
