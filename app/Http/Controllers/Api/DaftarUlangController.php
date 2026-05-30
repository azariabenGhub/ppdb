<?php

namespace App\Http\Controllers\Api;

use App\Helpers\FileEncryptionHelper;
use App\Http\Controllers\Controller;
use App\Models\CalonSiswa;
use App\Models\DaftarUlang;
use App\Models\DaftarUlangForm;
use App\Models\DaftarUlangOrangTua;
use App\Models\DaftarUlangWali;
use App\Models\Formulir;
use App\Models\SeleksiTes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

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

    // Ambil data form daftar ulang (header + detail sesuai tipe)
    public function getFormData(Request $request)
    {
        $user = $request->user();

        $seleksi = SeleksiTes::where('id_pendaftar', $user->id)->first();
        if (!$seleksi || $seleksi->kelulusan_tes !== 'lulus') {
            return response()->json(['eligible' => false, 'message' => 'Anda belum lulus seleksi.']);
        }

        // Cek apakah sudah ada data daftar ulang (file + isian)
        $daftarUlang = DaftarUlang::with(['orangTua', 'wali'])->where('user_id', $user->id)->first();

        if ($daftarUlang && ($daftarUlang->id_orang_tua || $daftarUlang->id_wali)) {
            if ($daftarUlang->id_orang_tua) {
                $detail = $daftarUlang->orangTua;
                $tipe = 'orang_tua';
                $header = [
                    'no_pendaftaran' => $daftarUlang->no_pendaftaran,
                    'nama_lengkap' => $detail->nama_lengkap,
                    'tempat_lahir' => $detail->tempat_lahir,
                    'tanggal_lahir' => $detail->tanggal_lahir,
                    'jenis_kelamin' => $detail->jenis_kelamin,
                    'asal_tk' => $detail->asal_tk,
                    'alamat_domisili' => $detail->alamat_domisili,
                    'is_bukan_pindahan' => $detail->is_bukan_pindahan,
                    'tipe_wali' => $tipe,
                ];
                return response()->json(['data' => $header, 'detail' => $detail, 'is_edit' => true]);
            } else {
                $detail = $daftarUlang->wali;
                $tipe = 'wali';
                $header = [
                    'no_pendaftaran' => $daftarUlang->no_pendaftaran,
                    'nama_lengkap' => $detail->nama_lengkap,
                    'tempat_lahir' => $detail->tempat_lahir,
                    'tanggal_lahir' => $detail->tanggal_lahir,
                    'jenis_kelamin' => $detail->jenis_kelamin,
                    'asal_tk' => $detail->asal_tk,
                    'alamat_domisili' => $detail->alamat_domisili,
                    'is_bukan_pindahan' => $detail->is_bukan_pindahan,
                    'tipe_wali' => $tipe,
                ];
                return response()->json(['data' => $header, 'detail' => $detail, 'is_edit' => true]);
            }
        }

        // Jika belum ada, ambil dari formulir pendaftaran awal
        $formulir = Formulir::with(['calonSiswa', 'ayah', 'ibu', 'wali'])
            ->whereHas('calonSiswa', fn($q) => $q->where('user_id', $user->id))
            ->first();

        if (!$formulir) {
            return response()->json(['eligible' => false, 'message' => 'Data pendaftaran tidak ditemukan.']);
        }

        $header = [
            'no_pendaftaran' => $formulir->no_pendaftaran,
            'nama_lengkap' => $formulir->calonSiswa->nama_lengkap,
            'tempat_lahir' => $formulir->calonSiswa->tempat_lahir,
            'tanggal_lahir' => $formulir->calonSiswa->tanggal_lahir,
            'jenis_kelamin' => $formulir->calonSiswa->jenis_kelamin, // tidak ada di formulir awal, biarkan kosong
            'asal_tk' => $formulir->calonSiswa->asal_tk,
            'alamat_domisili' => $formulir->calonSiswa->alamat_lengkap, // default dari alamat 
            'is_bukan_pindahan' => $formulir->is_bukan_pindahan,
            'tipe_wali' => $formulir->tipe_wali,
        ];

        if ($formulir->tipe_wali === 'orang_tua') {
            $detail = [
                // Data siswa tambahan tidak diisi
                'nama_ayah' => $formulir->ayah->nama ?? '',
                'pendidikan_ayah' => $formulir->ayah->pendidikan ?? '',
                'pekerjaan_ayah' => $formulir->ayah->pekerjaan ?? '',
                'alamat_ktp' => $formulir->ayah->alamat ?? '',
                'no_hp' => $formulir->ayah->no_telp ?? '',
                'nama_ibu' => $formulir->ibu->nama ?? '',
                'pendidikan_ibu' => $formulir->ibu->pendidikan ?? '',
                'pekerjaan_ibu' => $formulir->ibu->pekerjaan ?? '',
                'narahubung' => '', // kosong
                // Data tambahan untuk hidden fields (tidak wajib, opsional)
                'nik_ayah' => $formulir->ayah->nik ?? '',
                'agama_ayah' => $formulir->ayah->agama ?? '',
                'penghasilan_ayah' => $formulir->ayah->penghasilan ?? '',
                'alamat_ayah' => $formulir->ayah->alamat ?? '',
                'nik_ibu' => $formulir->ibu->nik ?? '',
                'agama_ibu' => $formulir->ibu->agama ?? '',
                'penghasilan_ibu' => $formulir->ibu->penghasilan ?? '',
                'no_telp_ibu' => $formulir->ibu->no_telp ?? '',
                'alamat_ibu' => $formulir->ibu->alamat ?? '',
            ];
        } else {
            $detail = [
                'nama_wali' => $formulir->wali->nama ?? '',
                'pendidikan_wali' => $formulir->wali->pendidikan ?? '',
                'pekerjaan_wali' => $formulir->wali->pekerjaan ?? '',
                'alamat_ktp' => $formulir->wali->alamat ?? '',
                'no_hp' => $formulir->wali->no_telp ?? '',
                'narahubung' => '',
                'nik_wali' => $formulir->wali->nik ?? '',
                'agama_wali' => $formulir->wali->agama ?? '',
                'penghasilan_wali' => $formulir->wali->penghasilan ?? '',
                'no_telp_wali' => $formulir->wali->no_telp ?? '',
                'alamat_wali' => $formulir->wali->alamat ?? '',
            ];
        }

        return response()->json(['data' => $header, 'detail' => $detail, 'is_edit' => false]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        // Cek kelulusan
        $seleksi = SeleksiTes::where('id_pendaftar', $user->id)->first();
        if (!$seleksi || $seleksi->kelulusan_tes !== 'lulus') {
            return response()->json(['message' => 'Anda tidak berhak daftar ulang.'], 403);
        }

        // Validasi file
        $request->validate([
            'akte_kelahiran' => 'required|file|mimes:pdf,jpg,png|max:2048',
            'ijazah_tk' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
            'ktp_orang_tua' => 'required|file|mimes:pdf,jpg,png|max:2048',
            'kartu_keluarga' => 'required|file|mimes:pdf,jpg,png|max:2048',
            'nisn_file' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
            'surat_pernyataan' => 'required|file|mimes:pdf,jpg,png|max:2048',
            'surat_pakta_integritas' => 'required|file|mimes:pdf,jpg,png|max:2048',
        ]);

        // Validasi data isian (disesuaikan dengan field yang ada di form)
        $rules = [
            'tipe_wali' => 'required|in:orang_tua,wali',
            'nama_lengkap' => 'required|string',
            'tempat_lahir' => 'required|string',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'nullable|string',
            'asal_tk' => 'nullable|string',
            'alamat_domisili' => 'required|string', // alamat lengkap domisili
            'is_bukan_pindahan' => 'nullable|boolean',
        ];

        if ($request->tipe_wali === 'orang_tua') {
            $rules = array_merge($rules, [
                'nama_ayah' => 'required|string',
                'pendidikan_ayah' => 'nullable|string',
                'pekerjaan_ayah' => 'nullable|string',
                'alamat_ktp' => 'nullable|string',
                'no_hp' => 'nullable|string',
                'nama_ibu' => 'required|string',
                'pendidikan_ibu' => 'nullable|string',
                'pekerjaan_ibu' => 'nullable|string',
                'narahubung' => 'nullable|string',
            ]);
        } else {
            $rules = array_merge($rules, [
                'nama_wali' => 'required|string',
                'pendidikan_wali' => 'nullable|string',
                'pekerjaan_wali' => 'nullable|string',
                'alamat_ktp' => 'nullable|string',
                'no_hp' => 'nullable|string',
                'narahubung' => 'nullable|string',
            ]);
        }

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            // Ambil no_pendaftaran dari formulir pendaftaran awal
            $formulir = Formulir::whereHas('calonSiswa', fn($q) => $q->where('user_id', $user->id))->first();
            if (!$formulir) {
                return response()->json(['message' => 'Data pendaftaran tidak ditemukan.'], 404);
            }

            // Cari atau buat record daftar_ulang (untuk file)
            $daftarUlang = DaftarUlang::where('user_id', $user->id)->first();
            if (!$daftarUlang) {
                $daftarUlang = new DaftarUlang();
                $daftarUlang->user_id = $user->id;
                $daftarUlang->no_pendaftaran = $formulir->no_pendaftaran;
            }

            // Proses upload file
            $noPendaftaran = $formulir->no_pendaftaran;
            $safeNo = str_replace('/', '-', $noPendaftaran);
            $tahun = date('Y');
            $baseDir = "daftar_ulang/{$tahun}/{$safeNo}";

            $daftarUlang->akte_kelahiran = FileEncryptionHelper::encryptAndStoreToPath($request->file('akte_kelahiran'), "$baseDir/akte_kelahiran");
            $daftarUlang->ijazah_tk = $request->hasFile('ijazah_tk') ? FileEncryptionHelper::encryptAndStoreToPath($request->file('ijazah_tk'), "$baseDir/ijazah_tk") : null;
            $daftarUlang->ktp_orang_tua = FileEncryptionHelper::encryptAndStoreToPath($request->file('ktp_orang_tua'), "$baseDir/ktp_orang_tua");
            $daftarUlang->kartu_keluarga = FileEncryptionHelper::encryptAndStoreToPath($request->file('kartu_keluarga'), "$baseDir/kartu_keluarga");
            $daftarUlang->nisn_file = $request->hasFile('nisn_file') ? FileEncryptionHelper::encryptAndStoreToPath($request->file('nisn_file'), "$baseDir/nisn") : null;
            $daftarUlang->surat_pernyataan = FileEncryptionHelper::encryptAndStoreToPath($request->file('surat_pernyataan'), "$baseDir/surat_pernyataan");
            $daftarUlang->surat_pakta_integritas = FileEncryptionHelper::encryptAndStoreToPath($request->file('surat_pakta_integritas'), "$baseDir/pakta_integritas");
            $daftarUlang->status = 'menunggu';
            $daftarUlang->save();

            // Simpan data siswa + orang tua/wali ke tabel terpisah
            if ($request->tipe_wali === 'orang_tua') {
                $orangTua = DaftarUlangOrangTua::updateOrCreate(
                    ['id' => $daftarUlang->id_orang_tua],
                    [
                        // Data siswa
                        'nama_lengkap' => $request->nama_lengkap,
                        'tempat_lahir' => $request->tempat_lahir,
                        'tanggal_lahir' => $request->tanggal_lahir,
                        'jenis_kelamin' => $request->jenis_kelamin,
                        'asal_sekolah' => $request->asal_sekolah,
                        'alamat_domisili' => $request->alamat_domisili,
                        'is_bukan_pindahan' => $request->is_bukan_pindahan ?? false,
                        // Data ayah
                        'nama_ayah' => $request->nama_ayah,
                        'pendidikan_ayah' => $request->pendidikan_ayah,
                        'pekerjaan_ayah' => $request->pekerjaan_ayah,
                        'alamat_ktp' => $request->alamat_ktp,
                        'no_hp' => $request->no_hp,
                        // Data ibu
                        'nama_ibu' => $request->nama_ibu,
                        'pendidikan_ibu' => $request->pendidikan_ibu,
                        'pekerjaan_ibu' => $request->pekerjaan_ibu,
                        'narahubung' => $request->narahubung,
                    ]
                );
                $daftarUlang->id_orang_tua = $orangTua->id;
                $daftarUlang->id_wali = null;
            } else {
                $wali = DaftarUlangWali::updateOrCreate(
                    ['id' => $daftarUlang->id_wali],
                    [
                        // Data siswa
                        'nama_lengkap' => $request->nama_lengkap,
                        'tempat_lahir' => $request->tempat_lahir,
                        'tanggal_lahir' => $request->tanggal_lahir,
                        'jenis_kelamin' => $request->jenis_kelamin,
                        'asal_sekolah' => $request->asal_sekolah,
                        'alamat_domisili' => $request->alamat_domisili,
                        'is_bukan_pindahan' => $request->is_bukan_pindahan ?? false,
                        // Data wali
                        'nama_wali' => $request->nama_wali,
                        'pendidikan_wali' => $request->pendidikan_wali,
                        'pekerjaan_wali' => $request->pekerjaan_wali,
                        'alamat_ktp' => $request->alamat_ktp,
                        'no_hp' => $request->no_hp,
                        'narahubung' => $request->narahubung,
                    ]
                );
                $daftarUlang->id_wali = $wali->id;
                $daftarUlang->id_orang_tua = null;
            }
            $daftarUlang->save();

            DB::commit();
            return response()->json(['message' => 'Daftar ulang berhasil dikirim.']);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Daftar ulang error: ' . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function getFormulirDaftarUlang($id)
    {
        $daftarUlang = DaftarUlang::with(['orangTua', 'wali'])->findOrFail($id);
        if ($daftarUlang->id_orang_tua) {
            $orangTua = $daftarUlang->orangTua;
            return response()->json([
                'tipe_wali' => 'orang_tua',
                'siswa' => [
                    'nama_lengkap' => $orangTua->nama_lengkap,
                    'tempat_lahir' => $orangTua->tempat_lahir,
                    'tanggal_lahir' => $orangTua->tanggal_lahir,
                    'jenis_kelamin' => $orangTua->jenis_kelamin,
                    'asal_sekolah' => $orangTua->asal_sekolah,
                    'alamat_domisili' => $orangTua->alamat_domisili,
                ],
                'orang_tua' => [
                    'nama_ayah' => $orangTua->nama_ayah,
                    'pendidikan_ayah' => $orangTua->pendidikan_ayah,
                    'pekerjaan_ayah' => $orangTua->pekerjaan_ayah,
                    'alamat_ktp' => $orangTua->alamat_ktp,
                    'no_hp' => $orangTua->no_hp,
                    'nama_ibu' => $orangTua->nama_ibu,
                    'pendidikan_ibu' => $orangTua->pendidikan_ibu,
                    'pekerjaan_ibu' => $orangTua->pekerjaan_ibu,
                    'narahubung' => $orangTua->narahubung,
                ]
            ]);
        } else {
            $wali = $daftarUlang->wali;
            return response()->json([
                'tipe_wali' => 'wali',
                'siswa' => [
                    'nama_lengkap' => $wali->nama_lengkap,
                    'tempat_lahir' => $wali->tempat_lahir,
                    'tanggal_lahir' => $wali->tanggal_lahir,
                    'jenis_kelamin' => $wali->jenis_kelamin,
                    'asal_sekolah' => $wali->asal_sekolah,
                    'alamat_domisili' => $wali->alamat_domisili,
                ],
                'wali' => [
                    'nama_wali' => $wali->nama_wali,
                    'pendidikan_wali' => $wali->pendidikan_wali,
                    'pekerjaan_wali' => $wali->pekerjaan_wali,
                    'alamat_ktp' => $wali->alamat_ktp,
                    'no_hp' => $wali->no_hp,
                    'narahubung' => $wali->narahubung,
                ]
            ]);
        }
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