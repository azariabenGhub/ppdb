<?php

namespace App\Http\Controllers\Api;

use App\Helpers\FileEncryptionHelper;
use App\Http\Controllers\Controller;
use App\Models\BuktiPembayaran;
use Illuminate\Http\Request;

class BuktiPembayaranController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'bukti_pembayaran' => 'required|image|mimes:jpg,png|max:2048',
            'jenis_pembayaran' => 'required|in:formulir,masuk',
        ]);
        
        $path = FileEncryptionHelper::encryptAndStore($request->file('bukti_pembayaran'), 'bukti_pembayaran');
        
        $bukti = BuktiPembayaran::create([
            'id_pendaftar' => $request->user()->id,
            'bukti_pembayaran' => $path,
            'jenis_pembayaran' => $request->jenis_pembayaran,
            'status' => 'menunggu',
        ]);
        return response()->json(['message' => 'Bukti berhasil diunggah', 'data' => $bukti], 201);
    }

    // Untuk pendaftar melihat riwayatnya
    public function index(Request $request)
    {
        $list = BuktiPembayaran::where('id_pendaftar', $request->user()->id)
            ->with('verifikasi.kwitansi')->get();
        return response()->json($list);
    }

    public function semua(Request $request) {
        $query = BuktiPembayaran::with('pendaftar', 'verifikasi.kwitansi');
        if ($request->status) $query->where('status', $request->status);
        return response()->json($query->get());
    }
}
