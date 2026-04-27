<?php
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FormulirController;
use Illuminate\Support\Facades\Route;

// Public routes (tidak memerlukan token)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes (memerlukan token valid)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/formulir', [FormulirController::class, 'store']);
    // Tambahkan rute API kalian di sini
});