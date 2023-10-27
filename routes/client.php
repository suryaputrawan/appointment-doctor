<?php

use App\Http\Controllers\Client\DoctorController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\HomeController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/profile-doctor/{id}', [DoctorController::class, 'show'])->name('doctor.show');
