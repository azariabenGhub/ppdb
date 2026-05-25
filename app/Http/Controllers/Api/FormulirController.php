<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ayah;
use App\Models\CalonSiswa;
use App\Models\Formulir;
use App\Models\Gelombang;
use App\Models\Ibu;
use App\Models\Wali;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FormulirController extends Controller
{
    public function index()
    {
        $formulir = Formulir::with(['calonSiswa.user', 'verifikasi'])->get();
        $data = $formulir->map(function ($item) {
            return [
                'id' => $item->id,
                'nama_pendaftar' => $item->calonSiswa->user->name ?? 'Tanpa Nama',
                'email_pendaftar' => $item->calonSiswa->user->email ?? '',
                'nama_lengkap' => $item->nama_lengkap, // accessor
                'tanggal_daftar' => $item->created_at->format('d-m-Y H:i'),
                'status' => $item->status ?? 'Baru',
            ];
        });
        return response()->json($data);
    }

    public function show($id)
    {
        $formulir = Formulir::with(['calonSiswa', 'ayah', 'ibu', 'wali', 'verifikasi'])->findOrFail($id);
        return response()->json(['data' => $formulir]);
    }

    public function store(Request $request)
    {
        $rules = [/* sama seperti sebelumnya */]; // aturan validasi tidak berubah
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = $request->user();
        $existing = Formulir::whereHas('calonSiswa', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->first();

        // Cek gelombang aktif dan kuota (sama seperti sebelumnya)
        $gelombang = Gelombang::where('status', 'aktif')
            ->where('periode_mulai', '<=', now())
            ->where('periode_selesai', '>=', now())
            ->first();
        if (!$gelombang) {
            return response()->json(['message' => 'Tidak ada gelombang pendaftaran aktif.'], 422);
        }
        $jumlahTerdaftar = Formulir::where('id_gelombang', $gelombang->id)
            ->whereIn('status', ['menunggu', 'diterima'])
            ->count();
        if ($jumlahTerdaftar >= $gelombang->kuota) {
            return response()->json(['message' => 'Kuota pendaftaran gelombang ini sudah penuh.'], 422);
        }

        DB::beginTransaction();
        try {
            if ($existing) {
                if (!in_array($existing->status, ['menunggu', 'ditolak'])) {
                    return response()->json(['message' => 'Formulir tidak dapat diubah.'], 403);
                }
                // Update data calon_siswa
                $calon = $existing->calonSiswa;
                $calon->update($request->only([
                    'nama_lengkap',
                    'tempat_lahir',
                    'tanggal_lahir',
                    'nik',
                    'agama',
                    'warga_negara',
                    'anak_ke',
                    'jumlah_saudara',
                    'alamat_lengkap'
                ]));
                // Update ayah/ibu/wali
                if ($request->tipe_wali === 'orang_tua') {
                    if ($existing->ayah)
                        $existing->ayah->update($request->only([
                            'nama_ayah',
                            'pekerjaan_ayah',
                            'agama_ayah',
                            'pendidikan_ayah',
                            'no_ktp_ayah',
                            'penghasilan_ayah',
                            'no_telp_ayah',
                            'alamat_ayah'
                        ]));
                    if ($existing->ibu)
                        $existing->ibu->update($request->only([
                            'nama_ibu',
                            'pekerjaan_ibu',
                            'agama_ibu',
                            'pendidikan_ibu',
                            'no_ktp_ibu',
                            'penghasilan_ibu',
                            'no_telp_ibu',
                            'alamat_ibu'
                        ]));
                } else {
                    if ($existing->wali)
                        $existing->wali->update($request->only([
                            'nama_wali',
                            'pekerjaan_wali',
                            'agama_wali',
                            'pendidikan_wali',
                            'no_ktp_wali',
                            'penghasilan_wali',
                            'no_telp_wali',
                            'alamat_wali'
                        ]));
                }
                // Update formulir (status, data pindahan)
                $existing->update([
                    'status' => 'menunggu',
                    'tipe_wali' => $request->tipe_wali,
                    'is_bukan_pindahan' => $request->is_bukan_pindahan,
                    'asal_sekolah' => $request->is_bukan_pindahan ? null : $request->asal_sekolah,
                    'no_ijazah' => $request->is_bukan_pindahan ? null : $request->no_ijazah,
                    'tahun_ijazah' => $request->is_bukan_pindahan ? null : $request->tahun_ijazah,
                    'diterima_kelas' => $request->is_bukan_pindahan ? null : $request->diterima_kelas,
                    'pindah_dari' => $request->is_bukan_pindahan ? null : $request->pindah_dari,
                    'no_pindah' => $request->is_bukan_pindahan ? null : $request->no_pindah,
                    'tanggal_pindah' => $request->is_bukan_pindahan ? null : $request->tanggal_pindah,
                ]);
                $existing->verifikasi()->delete();
                $pendaftaran = $existing;
            } else {
                // Buat calon_siswa
                $calon = CalonSiswa::create([
                    'user_id' => $user->id,
                    'nama_lengkap' => $request->nama_lengkap,
                    'tempat_lahir' => $request->tempat_lahir,
                    'tanggal_lahir' => $request->tanggal_lahir,
                    'nik' => $request->nik,
                    'agama' => $request->agama,
                    'warga_negara' => $request->warga_negara,
                    'anak_ke' => $request->anak_ke,
                    'jumlah_saudara' => $request->jumlah_saudara,
                    'alamat_lengkap' => $request->alamat_lengkap,
                ]);
                // Buat ayah/ibu atau wali
                $ayahId = null;
                $ibuId = null;
                $waliId = null;
                if ($request->tipe_wali === 'orang_tua') {
                    $ayah = Ayah::create([
                        'nama' => $request->nama_ayah,
                        'nik' => $request->no_ktp_ayah,
                        'pekerjaan' => $request->pekerjaan_ayah,
                        'agama' => $request->agama_ayah,
                        'pendidikan' => $request->pendidikan_ayah,
                        'penghasilan' => $request->penghasilan_ayah,
                        'no_telp' => $request->no_telp_ayah,
                        'alamat' => $request->alamat_ayah,
                    ]);
                    $ibu = Ibu::create([
                        'nama' => $request->nama_ibu,
                        'nik' => $request->no_ktp_ibu,
                        'pekerjaan' => $request->pekerjaan_ibu,
                        'agama' => $request->agama_ibu,
                        'pendidikan' => $request->pendidikan_ibu,
                        'penghasilan' => $request->penghasilan_ibu,
                        'no_telp' => $request->no_telp_ibu,
                        'alamat' => $request->alamat_ibu,
                    ]);
                    $ayahId = $ayah->id;
                    $ibuId = $ibu->id;
                } else {
                    $wali = Wali::create([
                        'nama' => $request->nama_wali,
                        'nik' => $request->no_ktp_wali,
                        'pekerjaan' => $request->pekerjaan_wali,
                        'agama' => $request->agama_wali,
                        'pendidikan' => $request->pendidikan_wali,
                        'penghasilan' => $request->penghasilan_wali,
                        'no_telp' => $request->no_telp_wali,
                        'alamat' => $request->alamat_wali,
                    ]);
                    $waliId = $wali->id;
                }
                // Generate nomor pendaftaran
                $tahun = date('Y');
                $count = Formulir::where('id_gelombang', $gelombang->id)->count();
                $noUrut = $count + 1;
                $noUrutPadded = str_pad($noUrut, 4, '0', STR_PAD_LEFT);
                $noPendaftaran = "PPDB/{$tahun}/{$gelombang->nomor_gelombang}/{$noUrutPadded}";

                $pendaftaran = Formulir::create([
                    'id_calon_siswa' => $calon->id,
                    'id_ayah' => $ayahId,
                    'id_ibu' => $ibuId,
                    'id_wali' => $waliId,
                    'no_pendaftaran' => $noPendaftaran,
                    'id_gelombang' => $gelombang->id,
                    'tipe_wali' => $request->tipe_wali,
                    'is_bukan_pindahan' => $request->is_bukan_pindahan,
                    'asal_sekolah' => $request->is_bukan_pindahan ? null : $request->asal_sekolah,
                    'no_ijazah' => $request->is_bukan_pindahan ? null : $request->no_ijazah,
                    'tahun_ijazah' => $request->is_bukan_pindahan ? null : $request->tahun_ijazah,
                    'diterima_kelas' => $request->is_bukan_pindahan ? null : $request->diterima_kelas,
                    'pindah_dari' => $request->is_bukan_pindahan ? null : $request->pindah_dari,
                    'no_pindah' => $request->is_bukan_pindahan ? null : $request->no_pindah,
                    'tanggal_pindah' => $request->is_bukan_pindahan ? null : $request->tanggal_pindah,
                    'status' => 'menunggu',
                ]);
            }
            DB::commit();
            return response()->json(['message' => 'Formulir berhasil disimpan.', 'data' => $pendaftaran], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Formulir store error: ' . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function myForm(Request $request)
    {
        $formulir = Formulir::with(['calonSiswa', 'ayah', 'ibu', 'wali', 'verifikasi'])
            ->whereHas('calonSiswa', function ($q) use ($request) {
                $q->where('user_id', $request->user()->id);
            })->first();
        if (!$formulir) {
            return response()->json(['data' => null, 'message' => 'Belum mengisi formulir.']);
        }
        return response()->json(['data' => $formulir]);
    }
}
