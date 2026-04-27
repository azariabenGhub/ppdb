<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BuktiPembayaranController;
use App\Http\Controllers\Api\FormulirController;
use App\Http\Controllers\Api\KwitansiController;
use App\Http\Controllers\Api\MetodePembayaranController;
use App\Http\Controllers\Api\PenilaianController;
use App\Http\Controllers\Api\PenjadwalanController;
use App\Http\Controllers\Api\VerifikasiController;
use App\Http\Controllers\Api\VerifikasiPembayaranController;
use App\Models\SeleksiTes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes (semua role yang sudah login)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/formulir', [FormulirController::class, 'store']);
    Route::get('/formulir-saya', [FormulirController::class, 'myForm']);

    // Bukti pembayaran (milik pendaftar sendiri)
    Route::get('/bukti-pembayaran', [BuktiPembayaranController::class, 'index']);
    Route::post('/bukti-pembayaran', [BuktiPembayaranController::class, 'store']);
    Route::get('/kwitansi', [KwitansiController::class, 'index']);

    // Melihat metode pembayaran (semua role)
    Route::get('/metode-pembayaran', [MetodePembayaranController::class, 'index']);

    Route::get('/seleksi-saya', function (Request $request) {
        $seleksi = SeleksiTes::with('penilaian')->where('id_pendaftar', $request->user()->id)->first();
        return response()->json($seleksi);
    });
});

// Khusus staf (panitia & kepala sekolah)
Route::middleware(['auth:sanctum', 'role:panitia,kepala_sekolah'])->group(function () {
    // metode pembayaran
    Route::post('/metode-pembayaran', [MetodePembayaranController::class, 'store']);
    Route::put('/metode-pembayaran/{id}', [MetodePembayaranController::class, 'update']);
    Route::delete('/metode-pembayaran/{id}', [MetodePembayaranController::class, 'destroy']);

    // Verifikasi formulir pendaftaran
    Route::post('/verifikasi', [VerifikasiController::class, 'store']);
    Route::get('/pendaftaran', [FormulirController::class, 'index']);
    Route::get('/pendaftaran/{id}', [FormulirController::class, 'show']);

    // Verifikasi pembayaran
    Route::get('/bukti-pembayaran/semua', [BuktiPembayaranController::class, 'semua']);
    Route::post('/verifikasi-pembayaran', [VerifikasiPembayaranController::class, 'verifikasi']);

    Route::get('/seleksi/eligible', [PenjadwalanController::class, 'eligible']);
    Route::get('/seleksi', [PenjadwalanController::class, 'index']);
    Route::post('/seleksi', [PenjadwalanController::class, 'store']);
    Route::put('/seleksi/{id}', [PenjadwalanController::class, 'update']);
    Route::get('/penilaian', [PenilaianController::class, 'index']);
    Route::post('/penilaian', [PenilaianController::class, 'store']);
});