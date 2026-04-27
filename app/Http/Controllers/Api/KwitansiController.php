<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kwitansi;
use Illuminate\Http\Request;

class KwitansiController extends Controller
{
    // Untuk pendaftar melihat kwitansi miliknya
    public function index(Request $request) {
        $kwitansis = Kwitansi::where('id_pendaftar', $request->user()->id)
                    ->with('verifikasi.buktiPembayaran')->get();
        return response()->json($kwitansis);
    }
    // Untuk unduh bisa gunakan route public dengan signed URL atau cukup akses path storage.
    }
