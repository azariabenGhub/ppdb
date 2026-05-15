<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/', function () {
    return redirect('/login');
});

Route::view('/login', 'auth.login');

// Rute untuk halaman registrasi awal (nama & email)
Route::get('/register-google', function () {
    return view('auth.register-google');
})->name('register.google');

// Proses form register-google: simpan data sementara lalu redirect ke halaman register lengkap
Route::post('/register-google', function (Request $request) {
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
    ]);

    // Redirect ke halaman registrasi lengkap dengan membawa data name & email sebagai query string
    return redirect()->to('/register?name=' . urlencode($request->name) . '&email=' . urlencode($request->email));
})->name('register.google.store');

// Rute untuk halaman registrasi lengkap (password, konfirmasi)
Route::get('/register', function (Request $request) {
    return view('auth.register', [
        'name' => $request->query('name', ''),
        'email' => $request->query('email', ''),
    ]);
})->name('register.full');

Route::view('/forgot-password', 'auth.forgot-password');
Route::get('/reset-password/{token}', function ($token) {
    return view('auth.reset-password', ['token' => $token]);
})->name('password.reset');

// Rute lainnya tetap seperti semula
Route::view('/forgot-password', 'auth.forgot-password');
Route::get('/reset-password/{token}', function ($token) {
    return view('auth.reset-password', ['token' => $token]);
})->name('password.reset');

Route::view('/dashboard', 'dashboard');
Route::view('/staff-dashboard', 'staff-dashboard');
Route::get('/formulir/{id}', function ($id) {
    return view('detail-formulir', ['id' => $id]);
});