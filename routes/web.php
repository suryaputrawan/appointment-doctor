<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\DoctorController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\HospitalController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SpecialityController;
use App\Http\Controllers\Permissions\RoleController;
use App\Http\Controllers\Permissions\UserController;
use App\Http\Controllers\Admin\AppointmentController;
use App\Http\Controllers\Permissions\AssignController;
use App\Http\Controllers\Admin\DoctorLocationController;
use App\Http\Controllers\Admin\DoctorEducationController;
use App\Http\Controllers\Admin\PracticeScheduleController;
use App\Http\Controllers\Permissions\PermissionController;
use App\Http\Controllers\Admin\UserController as AdminUserController;

Route::name('client.')->group(function () {
    include 'client.php';
});

Route::get('/admin', [AuthController::class, 'showloginform'])->name('login');
Route::post('/admin/postlogin', [AuthController::class, 'postlogin'])->name('postlogin');
Route::get('/admin/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/admin/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('forgot.password');
Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('reset.password');

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::group(['middleware'  => 'password_change'], function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::middleware('has.role')->group(function () {
            Route::resource('speciality', SpecialityController::class);
            Route::resource('doctor', DoctorController::class);

            Route::get('doctor-education/list/{id}', [DoctorEducationController::class, 'list'])->name('doctor-education.list');
            Route::resource('doctor-education', DoctorEducationController::class);

            Route::resource('services', ServiceController::class);
            Route::resource('hospitals', HospitalController::class);
            Route::resource('practice-schedules', PracticeScheduleController::class);

            // Route::get('doctor-location/list/{id}', [DoctorLocationController::class, 'list'])->name('doctor-location.list');
            Route::resource('doctor-location', DoctorLocationController::class);

            Route::group(['middleware' => [\Spatie\Permission\Middleware\PermissionMiddleware::using('arrived appointment')]], function () {
                Route::put('appointment/arrived/{id}', [AppointmentController::class, 'arrived'])->name('appointment.arrived');
            });

            Route::group(['middleware' => [\Spatie\Permission\Middleware\PermissionMiddleware::using('cancel appointment')]], function () {
                Route::put('appointment/cancel/{id}', [AppointmentController::class, 'cancel'])->name('appointment.cancel');
            });

            Route::resource('appointment', AppointmentController::class);

            Route::resource('users', AdminUserController::class);

            // Route role and permission
            Route::group(['middleware' => [\Spatie\Permission\Middleware\RoleMiddleware::using('Super Admin')]], function () {
                Route::prefix('roles-and-permission')->namespace('Permissions')->group(function () {
                    Route::prefix('roles')->group(function () {
                        Route::get('', [RoleController::class, 'index'])->name('roles.index');
                        Route::post('store', [RoleController::class, 'store'])->name('roles.store');
                        Route::get('{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
                        Route::put('{role}', [RoleController::class, 'update'])->name('roles.update');
                    });

                    Route::prefix('permissions')->group(function () {
                        Route::get('', [PermissionController::class, 'index'])->name('permissions.index');
                        Route::post('store', [PermissionController::class, 'store'])->name('permissions.store');
                        Route::get('{permission}/edit', [PermissionController::class, 'edit'])->name('permissions.edit');
                        Route::put('{permission}', [PermissionController::class, 'update'])->name('permissions.update');
                    });

                    Route::get('assignable', [AssignController::class, 'index'])->name('assign.index');
                    Route::get('assignable/create', [AssignController::class, 'create'])->name('assign.create');
                    Route::post('assignable/store', [AssignController::class, 'store'])->name('assign.store');
                    Route::get('assignable/{id}/edit', [AssignController::class, 'edit'])->name('assign.edit');
                    Route::put('assignable/{id}/update', [AssignController::class, 'update'])->name('assign.update');

                    // Route assign to user
                    Route::get('assign', [UserController::class, 'index'])->name('assign.user.index');
                    Route::get('assign/user', [UserController::class, 'create'])->name('assign.user.create');
                    Route::post('assign/user', [UserController::class, 'store'])->name('assign.user.store');
                    Route::get('assign/{id}/user', [UserController::class, 'edit'])->name('assign.user.edit');
                    Route::put('assign/{id}/user', [UserController::class, 'update'])->name('assign.user.update');
                });
            });
            // end route role permission

            //Profile Route
            Route::group(['prefix' => 'profile'], function () {
                Route::get('user', [ProfileController::class, 'edit'])->name('profile.edit');
                Route::put('user/{user}', [ProfileController::class, 'update'])->name('profile.update');
                Route::put('user/password/update', [ProfileController::class, 'updatePassword'])->name('password.update');
            });
        });
    });

    // Route change password first login
    Route::get('password/change', [AuthController::class, 'changePassword'])->name('password.change');
    Route::post('password/post_change', [AuthController::class, 'postChange'])->name('password.post_change');
    // End Route
});
