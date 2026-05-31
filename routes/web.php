<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleAuthController;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

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

Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('google.login');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);
Route::get('/auth/google/success', [GoogleAuthController::class, 'success'])->name('google.auth.success');

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
Route::get('/staff/pendaftar/{id}', function ($id) {
        return view('staff-pendaftar-detail', ['id' => $id]);
    })->name('staff.pendaftar.detail');