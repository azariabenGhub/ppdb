<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Gelombang;
use Illuminate\Http\Request;

class GelombangController extends Controller
{
    public function index()
    {
        return response()->json(Gelombang::orderBy('tahun', 'desc')->orderBy('nomor_gelombang')->get());
    }

    public function show($id)
    {
        $gelombang = Gelombang::findOrFail($id);
        return response()->json($gelombang);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomor_gelombang' => 'required|integer|min:1',
            'tahun' => 'required|integer|min:2000|max:2100',
            'periode_mulai' => 'required|date',
            'periode_selesai' => 'required|date|after:periode_mulai',
            'kuota' => 'required|integer|min:1',
            'biaya_formulir' => 'required|numeric|min:0',
            'biaya_daftar_ulang' => 'required|numeric|min:0',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $exists = Gelombang::where('nomor_gelombang', $request->nomor_gelombang)
            ->where('tahun', $request->tahun)
            ->exists();
        if ($exists) {
            return response()->json(['message' => 'Gelombang dengan nomor dan tahun tersebut sudah ada.'], 422);
        }

        $gelombang = Gelombang::create($request->all());
        return response()->json($gelombang, 201);
    }

    public function update(Request $request, $id)
    {
        $gelombang = Gelombang::findOrFail($id);
        $request->validate([
            'nomor_gelombang' => 'required|integer|min:1',
            'tahun' => 'required|integer|min:2000|max:2100',
            'periode_mulai' => 'required|date',
            'periode_selesai' => 'required|date|after:periode_mulai',
            'kuota' => 'required|integer|min:1',
            'biaya_formulir' => 'required|numeric|min:0',
            'biaya_daftar_ulang' => 'required|numeric|min:0',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $exists = Gelombang::where('nomor_gelombang', $request->nomor_gelombang)
            ->where('tahun', $request->tahun)
            ->where('id', '!=', $id)
            ->exists();
        if ($exists) {
            return response()->json(['message' => 'Gelombang dengan nomor dan tahun tersebut sudah ada.'], 422);
        }

        $gelombang->update($request->all());
        return response()->json($gelombang, 200);
    }

    public function destroy($id)
    {
        $gelombang = Gelombang::findOrFail($id);

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            // Dapatkan semua formulir yang menggunakan gelombang ini
            $formulirs = \App\Models\Formulir::where('id_gelombang', $gelombang->id)->get();
            $userIds = $formulirs->pluck('user_id')->toArray();

            // 1. Hapus data daftar ulang (dan file-file terkait di storage)
            $daftarUlangs = \App\Models\DaftarUlang::whereIn('user_id', $userIds)->get();
            foreach ($daftarUlangs as $du) {
                $fileFields = ['akte_kelahiran', 'ijazah_tk', 'ktp_orang_tua', 'kartu_keluarga', 'nisn_file', 'surat_pernyataan', 'surat_pakta_integritas'];
                foreach ($fileFields as $field) {
                    if ($du->$field) {
                        \Illuminate\Support\Facades\Storage::disk('private')->delete($du->$field);
                    }
                }
                
                $idOrangTua = $du->id_orang_tua;
                $idWali = $du->id_wali;
                
                $du->delete();
                
                if ($idOrangTua) {
                    \Illuminate\Support\Facades\DB::table('daftar_ulang_orang_tua')->where('id', $idOrangTua)->delete();
                }
                if ($idWali) {
                    \Illuminate\Support\Facades\DB::table('daftar_ulang_wali')->where('id', $idWali)->delete();
                }
            }

            // 2. Hapus seleksi tes dan penilaian
            $seleksis = \App\Models\SeleksiTes::whereIn('id_pendaftar', $userIds)->get();
            foreach ($seleksis as $sel) {
                \App\Models\Penilaian::where('id_seleksi_tes', $sel->id_seleksi_tes)->delete();
                $sel->delete();
            }

            // 3. Hapus bukti pembayaran, verifikasi pembayaran, dan kwitansi (serta file terkait)
            $buktiList = \App\Models\BuktiPembayaran::whereIn('id_pendaftar', $userIds)->get();
            foreach ($buktiList as $bukti) {
                if ($bukti->bukti_pembayaran) {
                    \Illuminate\Support\Facades\Storage::disk('private')->delete($bukti->bukti_pembayaran);
                }
                // Jika ada alias file_path
                if (isset($bukti->file_path) && $bukti->file_path) {
                    \Illuminate\Support\Facades\Storage::disk('private')->delete($bukti->file_path);
                }
                
                $verifikasi = \App\Models\VerifikasiPembayaran::where('id_bukti_pembayaran', $bukti->id_bukti_pembayaran)->first();
                if ($verifikasi) {
                    if ($verifikasi->kwitansi && $verifikasi->kwitansi->file_path) {
                        \Illuminate\Support\Facades\Storage::disk('private')->delete($verifikasi->kwitansi->file_path);
                    }
                    \App\Models\Kwitansi::where('id_verifikasi', $verifikasi->id_verifikasi)->delete();
                    $verifikasi->delete();
                }
                $bukti->delete();
            }

            // 4. Hapus formulir, calon siswa, ayah, ibu, wali, beserta verifikasi formulir
            foreach ($formulirs as $f) {
                \App\Models\VerifikasiFormulir::where('id_formulir', $f->id)->delete();
                
                $idCalonSiswa = $f->id_calon_siswa;
                $idAyah = $f->id_ayah;
                $idIbu = $f->id_ibu;
                $idWali = $f->id_wali;

                $f->delete();

                if ($idCalonSiswa) {
                    \App\Models\CalonSiswa::where('id', $idCalonSiswa)->delete();
                }
                if ($idAyah) {
                    \App\Models\Ayah::where('id', $idAyah)->delete();
                }
                if ($idIbu) {
                    \App\Models\Ibu::where('id', $idIbu)->delete();
                }
                if ($idWali) {
                    \App\Models\Wali::where('id', $idWali)->delete();
                }
            }

            // 5. Hapus user pendaftar terkait
            \App\Models\User::whereIn('id', $userIds)->delete();

            // 6. Hapus Gelombang
            $gelombang->delete();

            \Illuminate\Support\Facades\DB::commit();
            return response()->json(['message' => 'Gelombang beserta semua data terkait berhasil dihapus.'], 200);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Gagal hapus gelombang: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal menghapus gelombang: ' . $e->getMessage()], 500);
        }
    }

    public function toggleStatus($id)
    {
        $gelombang = Gelombang::findOrFail($id);
        $gelombang->status = $gelombang->status === 'aktif' ? 'nonaktif' : 'aktif';
        $gelombang->save();
        return response()->json(['message' => 'Status berubah.', 'data' => $gelombang]);
    }

    public function getAktif()
    {
        $now = now();
        $gelombang = Gelombang::where('status', 'aktif')
            ->where('periode_mulai', '<=', $now)
            ->where('periode_selesai', '>=', $now)
            ->first();

        // Kembalikan null dengan status 200 jika tidak ada
        return response()->json($gelombang ?: null, 200);
    }
}