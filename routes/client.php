<?php

use App\Http\Controllers\Client\AppointmentController;
use App\Http\Controllers\Client\DoctorController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\HomeController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('getBookingHospital', [AppointmentController::class, 'getBookingHospital'])->name('getBookingHospital');
Route::get('getBookingDate', [AppointmentController::class, 'getBookingDate'])->name('getBookingDate');
Route::get('getBookingTime', [AppointmentController::class, 'getBookingTime'])->name('getBookingTime');

Route::get('/profile-doctor/{id}', [DoctorController::class, 'show'])->name('doctor.show');

Route::post('/booking/store', [AppointmentController::class, 'store'])->name('appointment.store');
Route::get('/booking/{id}', [AppointmentController::class, 'booking'])->name('patient.booking');
Route::get('/booking/{id}/success', [AppointmentController::class, 'bookingSuccess'])->name('appointment.success');
