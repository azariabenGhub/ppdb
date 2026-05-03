<?php

namespace App\Http\Controllers\Api;

use App\Helpers\FileEncryptionHelper;
use App\Http\Controllers\Controller;
use App\Models\BuktiPembayaran;
use App\Models\Kwitansi;
use App\Models\VerifikasiPembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VerifikasiPembayaranController extends Controller
{
    public function verifikasi(Request $request)
    {
        $request->validate([
            'id_bukti_pembayaran' => 'required|exists:bukti_pembayarans,id_bukti_pembayaran',
            'hasil_verifikasi' => 'required|in:diterima,ditolak',
            'catatan' => 'nullable|string',
            'kwitansi' => 'required_if:hasil_verifikasi,diterima|file|mimes:pdf,jpg,png|max:2048',
        ]);
        $bukti = BuktiPembayaran::findOrFail($request->id_bukti_pembayaran);
        if ($bukti->status !== 'menunggu') {
            return response()->json(['message' => 'Bukti sudah diverifikasi'], 422);
        }
        DB::beginTransaction();
        try {
            $verifikasi = VerifikasiPembayaran::create([
                'id_bukti_pembayaran' => $bukti->id_bukti_pembayaran,
                'id_verifikator' => $request->user()->id,
                'hasil_verifikasi' => $request->hasil_verifikasi,
                'catatan' => $request->catatan,
            ]);
            $bukti->status = $request->hasil_verifikasi;
            $bukti->save();

            if ($request->hasil_verifikasi === 'diterima' && $request->hasFile('kwitansi')) {
                $path = FileEncryptionHelper::encryptAndStore($request->file('kwitansi'), 'kwitansi');
                Kwitansi::create([
                    'id_verifikasi_pembayaran' => $verifikasi->id_verifikasi_pembayaran,
                    'id_pendaftar' => $bukti->id_pendaftar,
                    'kwitansi' => $path,
                ]);
            }
            DB::commit();
            return response()->json(['message' => 'Verifikasi berhasil']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal: ' . $e->getMessage()], 500);
        }
    }
}
