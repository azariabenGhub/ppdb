<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // return view('welcome');

    return redirect('/login');
});

Route::view('/register', 'auth.register');
Route::view('/login', 'auth.login');

Route::view('/dashboard', 'dashboard');
Route::view('/staff-dashboard', 'staff-dashboard');