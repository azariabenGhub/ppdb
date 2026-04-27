<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // return view('welcome');

    return redirect('/login');
});

Route::view('/register', 'auth.register');
Route::view('/login', 'auth.login');

// Halaman dashboard (hanya bisa diakses jika ada token di localStorage, tidak ada middleware khusus di route)
Route::view('/dashboard', 'dashboard');