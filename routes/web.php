<?php

use App\Http\Controllers\Admin\AppointmentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\DoctorController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SpecialityController;
use App\Http\Controllers\Admin\DoctorLocationController;
use App\Http\Controllers\Admin\DoctorEducationController;
use App\Http\Controllers\Admin\HospitalController;
use App\Http\Controllers\Admin\PracticeScheduleController;

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
    Route::resource('hospitals', HospitalController::class);
    Route::resource('practice-schedules', PracticeScheduleController::class);

    // Route::get('doctor-location/list/{id}', [DoctorLocationController::class, 'list'])->name('doctor-location.list');
    Route::resource('doctor-location', DoctorLocationController::class);

    Route::put('appointment/arrived/{id}', [AppointmentController::class, 'arrived'])->name('appointment.arrived');
    Route::put('appointment/cancel/{id}', [AppointmentController::class, 'cancel'])->name('appointment.cancel');
    Route::resource('appointment', AppointmentController::class);
});
