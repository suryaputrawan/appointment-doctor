<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('client.home');
});

Route::get('/admin', function () {
    return view('admin.dashboard');
});
