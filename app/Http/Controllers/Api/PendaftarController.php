<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VerifikasiFormulir;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\CalonSiswa;
use App\Models\Formulir;
use App\Models\BuktiPembayaran;
use App\Models\SeleksiTes;
use App\Models\Penilaian;
use App\Models\DaftarUlang;
use App\Models\Kwitansi;
use App\Models\VerifikasiPembayaran;
use Illuminate\Http\Request;

class PendaftarController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'pendaftar');

        // SEARCH: cari berdasarkan name, email, atau no_pendaftaran (via formulir)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('formulir', function ($q2) use ($search) {
                        $q2->where('no_pendaftaran', 'like', "%{$search}%");
                    });
            });
        }

        // Filter tahun (berdasarkan created_at user atau tahun pendaftaran? Biarkan berdasarkan user)
        if ($request->filled('tahun')) {
            $query->whereYear('created_at', $request->tahun);
        }

        if ($request->filled('filter_nisn')) {
            if ($request->filter_nisn === 'ya') {
                $query->whereHas('formulir.calonSiswa', fn($q) => $q->where('punya_nisn', true));
            } elseif ($request->filter_nisn === 'tidak') {
                $query->where(function($q) {
                    $q->whereDoesntHave('formulir.calonSiswa')
                    ->orWhereHas('formulir.calonSiswa', fn($sq) => $sq->where('punya_nisn', false));
                });
            }
        }

        // Filter gelombang
        if ($request->filled('gelombang')) {
            $query->whereHas('formulir', fn($q) => $q->where('id_gelombang', $request->gelombang));
        }

        // Filter status formulir
        if ($request->filled('status_formulir')) {
            if ($request->status_formulir === 'sudah') {
                $query->whereHas('formulir');
            } elseif ($request->status_formulir === 'belum') {
                $query->whereDoesntHave('formulir');
            }
        }

        // Filter kelulusan (via seleksiTes)
        if ($request->filled('kelulusan')) {
            if ($request->kelulusan === 'belum') {
                $query->whereDoesntHave('seleksiTes');
            } else {
                $query->whereHas('seleksiTes', fn($q) => $q->where('kelulusan_tes', $request->kelulusan));
            }
        }

        // Filter daftar ulang
        if ($request->filled('status_daftar_ulang')) {
            switch ($request->status_daftar_ulang) {
                case 'sudah':
                    $query->whereHas('daftarUlang');
                    break;
                case 'belum':
                    $query->whereDoesntHave('daftarUlang');
                    break;
                default:
                    $query->whereHas('daftarUlang', fn($q) => $q->where('status', $request->status_daftar_ulang));
                    break;
            }
        }

        // Sorting: untuk no_pendaftaran perlu join ke formulir melalui calon_siswa
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        if ($sortBy === 'no_pendaftaran') {
            $query->leftJoin('formulirs', 'users.id', '=', 'formulirs.user_id')
                  ->select('users.*', 'formulirs.no_pendaftaran')
                  ->orderBy('formulirs.no_pendaftaran', $sortOrder);
        } elseif ($sortBy === 'name') {
            $query->orderBy('users.name', $sortOrder);
        } elseif ($sortBy === 'email') {
            $query->orderBy('users.email', $sortOrder);
        } else {
            $query->orderBy('users.created_at', $sortOrder);
        }

        $pendaftar = $query->paginate(15);

        // Transform hasil: tambahkan field tambahan
        $pendaftar->getCollection()->transform(function ($user) {
            $user->no_pendaftaran = $user->formulir ? $user->formulir->no_pendaftaran : null;
            $user->status_formulir = $user->formulir ? $user->formulir->status : 'belum_isi';
            $user->kelulusan = $user->seleksiTes ? $user->seleksiTes->kelulusan_tes : null;
            $user->status_daftar_ulang = $user->daftarUlang ? $user->daftarUlang->status : null;
            return $user;
        });

        return response()->json($pendaftar);
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'Pendaftar tidak ditemukan.'], 404);
        }

        // Pastikan yang dihapus adalah role 'calon_siswa' (bukan staff)
        if (in_array($user->role, ['panitia', 'bendahara', 'kepala_sekolah'])) {
            return response()->json(['message' => 'Tidak dapat menghapus akun staff.'], 403);
        }

        DB::beginTransaction();
        try {
            // 1. Hapus data daftar ulang (dan file-file terkait di storage)
            $daftarUlang = DaftarUlang::where('user_id', $user->id)->first();
            if ($daftarUlang) {
                // Hapus file-file di storage (private disk)
                $fileFields = ['akte_kelahiran', 'ijazah_tk', 'ktp_orang_tua', 'kartu_keluarga', 'nisn_file', 'surat_pernyataan', 'surat_pakta_integritas'];
                foreach ($fileFields as $field) {
                    if ($daftarUlang->$field) {
                        \Storage::disk('private')->delete($daftarUlang->$field);
                    }
                }
                $daftarUlang->delete();
            }

            // 2. Hapus seleksi tes dan penilaian
            $seleksi = SeleksiTes::where('id_pendaftar', $user->id)->first();
            if ($seleksi) {
                $penilaian = Penilaian::where('id_seleksi_tes', $seleksi->id_seleksi_tes)->first();
                if ($penilaian) {
                    $penilaian->delete();
                }
                $seleksi->delete();
            }

            // 3. Hapus bukti pembayaran, verifikasi pembayaran, dan kwitansi
            $buktiList = BuktiPembayaran::where('id_pendaftar', $user->id)->get();
            foreach ($buktiList as $bukti) {
                // Hapus file bukti dari storage
                if ($bukti->file_path) {
                    \Storage::disk('private')->delete($bukti->file_path);
                }
                // Hapus verifikasi pembayaran & kwitansi
                $verifikasiPembayaran = VerifikasiPembayaran::where('id_bukti_pembayaran', $bukti->id_bukti_pembayaran)->first();
                if ($verifikasiPembayaran) {
                    if ($verifikasiPembayaran->kwitansi && $verifikasiPembayaran->kwitansi->file_path) {
                        \Storage::disk('private')->delete($verifikasiPembayaran->kwitansi->file_path);
                    }
                    Kwitansi::where('id_verifikasi', $verifikasiPembayaran->id_verifikasi)->delete();
                    $verifikasiPembayaran->delete();
                }
                $bukti->delete();
            }

            // 4. Hapus formulir dan calon siswa, beserta verifikasi formulir
            $formulir = Formulir::where('user_id', $user->id)->first();
            if ($formulir) {
                $verifikasiFormulir = VerifikasiFormulir::where('id_formulir', $formulir->id_formulir)->first();
                if ($verifikasiFormulir) {
                    $verifikasiFormulir->delete();
                }
                $calonSiswa = CalonSiswa::find($formulir->id_calon_siswa);
                $formulir->delete();
                if ($calonSiswa) {
                    $calonSiswa->delete();
                }
            }

            // 6. Hapus user
            $user->delete();

            DB::commit();
            return response()->json(['message' => 'Pendaftar beserta seluruh datanya berhasil dihapus.'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Gagal hapus pendaftar: ' . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $user = User::where('role', 'pendaftar')
            ->with([
                'formulir.verifikasi',
                'formulir.gelombang',
                'seleksiTes.penilaian',
                'daftarUlang',
                'buktiPembayaran.verifikasi.kwitansi'
            ])
            ->findOrFail($id);

        $user->no_pendaftaran = $user->formulir?->no_pendaftaran;
        $user->status_formulir = $user->formulir?->status ?? 'belum_isi';
        $user->kelulusan = $user->seleksiTes?->kelulusan_tes;
        $user->status_daftar_ulang = $user->daftarUlang?->status;

        return response()->json($user);
    }

    public function tahunOptions()
    {
        $tahun = User::where('role', 'pendaftar')
            ->selectRaw('YEAR(created_at) as tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');
        return response()->json($tahun);
    }

    public function formulir($id)
    {
        $user = User::where('role', 'pendaftar')->findOrFail($id);
        // Ambil formulir melalui relasi yang sudah didefinisikan
        $formulir = $user->formulir()->with('verifikasi')->first();
        return response()->json($formulir);
    }

    public function dokumenDaftarUlang($id)
    {
        $user = User::find($id);
        if (!$user) return response()->json(['message' => 'User not found'], 404);
        
        $du = DaftarUlang::where('user_id', $user->id)
            ->with(['orangTua', 'wali'])
            ->first();
        
        if (!$du) {
            return response()->json(['message' => 'Belum daftar ulang'], 404);
        }
        
        return response()->json($du);
    }

    public function getStats(Request $request)
    {
        $now = now();
        $gelombang = \App\Models\Gelombang::where('status', 'aktif')
            ->where('periode_mulai', '<=', $now)
            ->where('periode_selesai', '>=', $now)
            ->first();

        $stats = [
            'total_pendaftar' => User::where('role', 'pendaftar')->count(),
            'formulir_menunggu' => Formulir::where('status', 'menunggu')->count(),
            'formulir_diterima' => Formulir::where('status', 'diterima')->count(),
            'pembayaran_menunggu' => BuktiPembayaran::where('status', 'menunggu')->count(),
            'pembayaran_diterima' => BuktiPembayaran::where('status', 'diterima')->count(),
            'daftar_ulang_menunggu' => DaftarUlang::where('status', 'menunggu')->count(),
            'daftar_ulang_diterima' => DaftarUlang::where('status', 'diterima')->count(),
            'gelombang_aktif' => $gelombang ? [
                'nomor' => $gelombang->nomor_gelombang,
                'tahun' => $gelombang->tahun,
                'kuota' => $gelombang->kuota,
                'sisa_kuota' => $gelombang->sisa_kuota,
            ] : null
        ];

        return response()->json($stats);
    }
}