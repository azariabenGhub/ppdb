<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DaftarUlang;

use App\Models\User;
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
            $query->leftJoin('calon_siswa', 'users.id', '=', 'calon_siswa.user_id')
                  ->leftJoin('formulirs', 'calon_siswa.id', '=', 'formulirs.id_calon_siswa')
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
        $user = User::where('role', 'pendaftar')->findOrFail($id);
        $daftarUlang = DaftarUlang::where('user_id', $user->id)->first();
        return response()->json($daftarUlang);
    }
}