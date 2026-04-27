<?php
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FormulirController;
use App\Http\Controllers\Api\VerifikasiController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/formulir', [FormulirController::class, 'store']);
    Route::get('/formulir-saya', [FormulirController::class, 'myForm']);
    
});

Route::middleware(['auth:sanctum', 'role:panitia,kepala_sekolah'])->group(function () {
    Route::post('/verifikasi', [VerifikasiController::class, 'store']);
    Route::get('/pendaftaran', [FormulirController::class, 'index']);
    Route::get('/pendaftaran/{id}', [FormulirController::class, 'show']);
});