<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DaftarUlang;
use App\Models\SeleksiTes;
use App\Helpers\FileEncryptionHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DaftarUlangController extends Controller
{
    // Pendaftar: cek apakah sudah lulus dan belum daftar ulang
    public function cekStatus(Request $request)
    {
        $user = $request->user();
        $seleksi = SeleksiTes::where('id_pendaftar', $user->id)->first();
        if (!$seleksi || $seleksi->kelulusan_tes !== 'lulus') {
            return response()->json(['eligible' => false, 'message' => 'Anda belum lulus seleksi.']);
        }
        $daftarUlang = DaftarUlang::where('user_id', $user->id)->first();
        return response()->json([
            'eligible' => true,
            'sudah_mengirim' => $daftarUlang ? true : false,
            'status' => $daftarUlang ? $daftarUlang->status : null,
        ]);
    }

    // Pendaftar: simpan dokumen daftar ulang
    public function store(Request $request)
    {
        $user = $request->user();
        // Validasi kelulusan
        $seleksi = SeleksiTes::where('id_pendaftar', $user->id)->first();
        if (!$seleksi || $seleksi->kelulusan_tes !== 'lulus') {
            return response()->json(['message' => 'Anda tidak berhak daftar ulang.'], 403);
        }
        $existing = DaftarUlang::where('user_id', $user->id)->first();
        if ($existing && $existing->status !== 'ditolak') {
            return response()->json(['message' => 'Anda sudah mengirim daftar ulang.'], 422);
        }

        $request->validate([
            'akte_kelahiran' => 'required|file|mimes:pdf,jpg,png|max:2048',
            'ijazah_tk' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
            'ktp_orang_tua' => 'required|file|mimes:pdf,jpg,png|max:2048',
            'kartu_keluarga' => 'required|file|mimes:pdf,jpg,png|max:2048',
            'nisn_file' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
            'surat_pernyataan' => 'required|file|mimes:pdf,jpg,png|max:2048',
            'surat_pakta_integritas' => 'required|file|mimes:pdf,jpg,png|max:2048',
        ]);

        $pathAkte = FileEncryptionHelper::encryptAndStore($request->file('akte_kelahiran'), 'daftar_ulang');
        $pathIjazah = $request->hasFile('ijazah_tk') ? FileEncryptionHelper::encryptAndStore($request->file('ijazah_tk'), 'daftar_ulang') : null;
        $pathKtp = FileEncryptionHelper::encryptAndStore($request->file('ktp_orang_tua'), 'daftar_ulang');
        $pathKk = FileEncryptionHelper::encryptAndStore($request->file('kartu_keluarga'), 'daftar_ulang');
        $pathNisn = FileEncryptionHelper::encryptAndStore($request->file('nisn_file'), 'daftar_ulang');
        $pathSuratPernyataan = FileEncryptionHelper::encryptAndStore($request->file('surat_pernyataan'), 'daftar_ulang');
        $pathPakta = FileEncryptionHelper::encryptAndStore($request->file('surat_pakta_integritas'), 'daftar_ulang');

        $data = [
            'user_id' => $user->id,
            'akte_kelahiran' => $pathAkte,
            'ijazah_tk' => $pathIjazah,
            'ktp_orang_tua' => $pathKtp,
            'kartu_keluarga' => $pathKk,
            'nisn_file' =>$pathNisn,
            'surat_pernyataan' => $pathSuratPernyataan,
            'surat_pakta_integritas' => $pathPakta,
            'status' => 'menunggu',
        ];

        if ($existing) {
            $existing->update($data);
            $daftar = $existing;
        } else {
            $daftar = DaftarUlang::create($data);
        }

        return response()->json(['message' => 'Berkas daftar ulang terkirim.', 'data' => $daftar], 201);
    }

    // Pendaftar: lihat status daftar ulang
    public function index(Request $request)
    {
        $daftar = DaftarUlang::where('user_id', $request->user()->id)->first();
        return response()->json($daftar);
    }

    // STAFF: semua daftar ulang
    public function semua()
    {
        $list = DaftarUlang::with('user')->orderBy('created_at', 'desc')->get();
        return response()->json($list);
    }

    // STAFF: verifikasi
    public function verifikasi(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:diterima,ditolak',
            'catatan' => 'nullable|string'
        ]);
        $daftar = DaftarUlang::findOrFail($id);
        $daftar->status = $request->status;
        $daftar->catatan = $request->catatan;
        $daftar->save();
        return response()->json(['message' => 'Verifikasi berhasil.']);
    }
}