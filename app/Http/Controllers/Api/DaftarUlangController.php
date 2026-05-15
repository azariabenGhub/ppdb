<?php

namespace App\Http\Controllers\Api;

use App\Helpers\FileEncryptionHelper;
use App\Http\Controllers\Controller;
use App\Models\DaftarUlang;
use App\Models\Formulir;
use App\Models\SeleksiTes;
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

        // Ambil nomor pendaftaran dari formulir
        $formulir = Formulir::where('user_id', $user->id)->first();
        if (!$formulir || !$formulir->no_pendaftaran) {
            return response()->json(['message' => 'Nomor pendaftaran tidak ditemukan.'], 422);
        }
        
        $noPendaftaran = $formulir->no_pendaftaran;
        // Bersihkan karakter yang tidak valid untuk nama folder (ganti slash dengan dash)
        $safeNo = str_replace('/', '-', $noPendaftaran);
        $tahun = date('Y');
        $baseDir = "daftar_ulang/{$tahun}/{$safeNo}";

        // Simpan file menggunakan path kustom
        $pathAkte = FileEncryptionHelper::encryptAndStoreToPath($request->file('akte_kelahiran'), $baseDir . '/akte_kelahiran');
        $pathIjazah = $request->hasFile('ijazah_tk') ? FileEncryptionHelper::encryptAndStoreToPath($request->file('ijazah_tk'), $baseDir . '/ijazah_tk') : null;
        $pathKtp = FileEncryptionHelper::encryptAndStoreToPath($request->file('ktp_orang_tua'), $baseDir . '/ktp_orang_tua');
        $pathKk = FileEncryptionHelper::encryptAndStoreToPath($request->file('kartu_keluarga'), $baseDir . '/kartu_keluarga');
        $pathNisn = $request->hasFile('nisn_file') ? FileEncryptionHelper::encryptAndStoreToPath($request->file('nisn_file'), $baseDir . '/nisn') : null;
        $pathSuratPernyataan = FileEncryptionHelper::encryptAndStoreToPath($request->file('surat_pernyataan'), $baseDir . '/surat_pernyataan');
        $pathPakta = FileEncryptionHelper::encryptAndStoreToPath($request->file('surat_pakta_integritas'), $baseDir . '/pakta_integritas');

        $data = [
            'user_id' => $user->id,
            'akte_kelahiran' => $pathAkte,
            'ijazah_tk' => $pathIjazah,
            'ktp_orang_tua' => $pathKtp,
            'kartu_keluarga' => $pathKk,
            'nisn_file' => $pathNisn,
            'surat_pernyataan' => $pathSuratPernyataan,
            'surat_pakta_integritas' => $pathPakta,
            'status' => 'menunggu',
        ];

        if ($existing) {
            // Hapus file lama sebelum update
            $this->deleteOldFiles($existing);
            $existing->update($data);
            $daftar = $existing;
        } else {
            $daftar = DaftarUlang::create($data);
        }

        return response()->json(['message' => 'Berkas daftar ulang terkirim.', 'data' => $daftar], 201);
    }

    private function deleteOldFiles($daftarUlang)
    {
        $fields = ['akte_kelahiran', 'ijazah_tk', 'ktp_orang_tua', 'kartu_keluarga', 'nisn_file', 'surat_pernyataan', 'surat_pakta_integritas'];
        foreach ($fields as $field) {
            if ($daftarUlang->$field && Storage::disk('private')->exists($daftarUlang->$field)) {
                Storage::disk('private')->delete($daftarUlang->$field);
            }
        }
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