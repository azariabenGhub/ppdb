<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BuktiPembayaran;
use App\Models\User;
use App\Models\DaftarUlang;
use App\Models\Formulir;
use App\Helpers\FileEncryptionHelper;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PendaftarLulusExport;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use Illuminate\Support\Facades\Log;

class LaporanController extends Controller
{
    // Download Excel pendaftar lulus
    public function exportExcel(Request $request)
    {
        $columns = $request->input('columns', []);
        $filters = $request->only(['tahun', 'gelombang', 'status_formulir', 'kelulusan', 'status_daftar_ulang', 'nisn', 'search']);
        return Excel::download(new PendaftarLulusExport($columns, $filters), 'pendaftar_lulus.xlsx');
    }

    public function getAvailableColumns()
    {
        $export = new PendaftarLulusExport();
        return response()->json($export->allColumns());
    }

    // Download ZIP semua dokumen daftar ulang dengan struktur folder
    public function downloadArsipDaftarUlang()
    {
        // Load relasi user, formulir, dan gelombang dari formulir
        $daftarUlangList = DaftarUlang::with(['user.formulir.gelombang'])
            ->where('status', 'diterima')
            ->get();

        if ($daftarUlangList->isEmpty()) {
            return response()->json(['message' => 'Tidak ada data daftar ulang yang diterima.'], 404);
        }

        $zip = new ZipArchive();
        $zipName = 'arsip_daftar_ulang_' . date('Ymd_His') . '.zip';
        $zipPath = storage_path('app/temp/' . $zipName);

        if (!is_dir(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return response()->json(['message' => 'Gagal membuat file ZIP.'], 500);
        }

        foreach ($daftarUlangList as $du) {
            $user = $du->user;
            $formulir = $user->formulir;

            if (!$formulir || !$formulir->no_pendaftaran) {
                continue;
            }

            $noPendaftaran = $formulir->no_pendaftaran;
            $tahun = $formulir->created_at ? $formulir->created_at->format('Y') : date('Y');
            $gelombang = $formulir->gelombang ? $formulir->gelombang->nomor_gelombang : null;

            // Fallback: jika gelombang masih null, coba ekstrak dari nomor pendaftaran (format: PPDB/2026/1/0001)
            if (!$gelombang && preg_match('/\/(\d+)\/\d{4}$/', $noPendaftaran, $matches)) {
                $gelombang = $matches[1];
            }

            $gelombangLabel = $gelombang ? "Gelombang_{$gelombang}" : 'Gelombang_unknown';
            $safeNoPendaftaran = str_replace('/', '-', $noPendaftaran);
            $folderPath = "{$tahun}/{$gelombangLabel}/{$safeNoPendaftaran}";

            // Daftar file yang akan ditambahkan (sesuai field di tabel daftar_ulang)
            $files = [
                'akte_kelahiran' => $du->akte_kelahiran,
                'ijazah_tk' => $du->ijazah_tk,
                'ktp_orang_tua' => $du->ktp_orang_tua,
                'kartu_keluarga' => $du->kartu_keluarga,
                'nisn_file' => $du->nisn_file,
                'surat_pernyataan' => $du->surat_pernyataan,
                'surat_pakta_integritas' => $du->surat_pakta_integritas,
            ];

            foreach ($files as $key => $path) {
                if (!$path)
                    continue;
                if (!Storage::disk('private')->exists($path))
                    continue;

                $content = FileEncryptionHelper::getDecryptedContent($path);
                if ($content === false)
                    continue;

                $extension = $this->detectExtension($path, $content);
                $fileName = $key . $extension;
                $zip->addFromString($folderPath . '/' . $fileName, $content);
            }
        }

        $zip->close();

        return response()->download($zipPath, $zipName)->deleteFileAfterSend(true);
    }

    public function downloadArsipPembayaran(Request $request)
    {
        $query = BuktiPembayaran::where('status', 'diterima')
            ->with([
                'pendaftar.formulir.calonSiswa',
                'pendaftar.formulir.ayah',
                'pendaftar.formulir.ibu',
                'pendaftar.formulir.wali',
                'pendaftar.formulir.gelombang',
                'verifikasi.kwitansi'
            ]);

        // Filter jenis pembayaran
        if ($request->filled('jenis')) {
            $query->where('jenis_pembayaran', $request->jenis);
        }

        // Filter tahun (dari created_at bukti pembayaran)
        if ($request->filled('tahun')) {
            $query->whereYear('created_at', $request->tahun);
        }

        // Filter gelombang (via relasi formulir)
        if ($request->filled('gelombang')) {
            $query->whereHas('pendaftar.formulir', function($q) use ($request) {
                $q->where('id_gelombang', $request->gelombang);
            });
        }

        $buktiList = $query->get();

        if ($buktiList->isEmpty()) {
            return response()->json(['message' => 'Tidak ada data pembayaran yang sesuai filter.'], 404);
        }

        $zip = new ZipArchive();
        $zipName = 'arsip_pembayaran_' . date('Ymd_His') . '.zip';
        $zipPath = storage_path('app/temp/' . $zipName);

        if (!is_dir(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return response()->json(['message' => 'Gagal membuat file ZIP.'], 500);
        }

        foreach ($buktiList as $bukti) {
            $user = $bukti->pendaftar;
            $formulir = $user->formulir;
            if (!$formulir || !$formulir->no_pendaftaran) {
                continue;
            }

            $calon = $formulir->calonSiswa;
            $ayah = $formulir->ayah;
            $ibu = $formulir->ibu;
            $wali = $formulir->wali;
            $tipeWali = $formulir->tipe_wali;

            // Nama folder berdasarkan tipe wali
            $namaFolder = $formulir->no_pendaftaran;
            if ($tipeWali === 'orang_tua') {
                $namaFolder .= '_' . ($ayah ? str_replace(' ', '_', $ayah->nama) : 'ayah');
                $namaFolder .= '_' . ($ibu ? str_replace(' ', '_', $ibu->nama) : 'ibu');
            } else {
                $namaFolder .= '_' . ($wali ? str_replace(' ', '_', $wali->nama) : 'wali');
                $namaFolder .= '_' . ($ibu ? str_replace(' ', '_', $ibu->nama) : 'ibu');
            }
            $namaFolder .= '_' . ($calon ? str_replace(' ', '_', $calon->nama_lengkap) : 'siswa');
            // Hapus karakter berbahaya untuk nama folder
            $namaFolder = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $namaFolder);

            $tahun = $bukti->created_at ? $bukti->created_at->format('Y') : date('Y');
            $gelombang = $formulir->gelombang ? $formulir->gelombang->nomor_gelombang : 'unknown';
            $jenis = $bukti->jenis_pembayaran; // 'formulir' atau 'masuk'

            // Struktur folder
            $basePath = "pembayaran/{$tahun}/Gelombang_{$gelombang}/{$namaFolder}/{$jenis}";

            // Tambahkan file bukti pembayaran
            if ($bukti->bukti_pembayaran && Storage::disk('private')->exists($bukti->bukti_pembayaran)) {
                $content = FileEncryptionHelper::getDecryptedContent($bukti->bukti_pembayaran);
                if ($content !== false) {
                    $extension = $this->detectExtension($bukti->bukti_pembayaran, $content);
                    $fileName = "bukti_pembayaran{$extension}";
                    $zip->addFromString($basePath . '/' . $fileName, $content);
                }
            }

            // Tambahkan kwitansi jika ada
            if ($bukti->verifikasi && $bukti->verifikasi->kwitansi) {
                $kwitansi = $bukti->verifikasi->kwitansi;
                if ($kwitansi->kwitansi && Storage::disk('private')->exists($kwitansi->kwitansi)) {
                    $content = FileEncryptionHelper::getDecryptedContent($kwitansi->kwitansi);
                    if ($content !== false) {
                        $extension = $this->detectExtension($kwitansi->kwitansi, $content);
                        $fileName = "kwitansi{$extension}";
                        $zip->addFromString($basePath . '/' . $fileName, $content);
                    }
                }
            }
        }

        $zip->close();

        return response()->download($zipPath, $zipName)->deleteFileAfterSend(true);
    }

    public function tahunOptions()
    {
        $tahun = BuktiPembayaran::selectRaw('YEAR(created_at) as tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');
        return response()->json($tahun);
    }

    // Optional: endpoint untuk mendapatkan opsi gelombang (dari formulir yang memiliki pembayaran)
    public function gelombangOptions()
    {
        $gelombangIds = BuktiPembayaran::whereHas('pendaftar.formulir')
            ->with('pendaftar.formulir.gelombang')
            ->get()
            ->pluck('pendaftar.formulir.gelombang')
            ->filter()
            ->unique('id')
            ->values();
        return response()->json($gelombangIds);
    }

    private function detectExtension($path, $content)
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_buffer($finfo, $content);
        finfo_close($finfo);

        $map = [
            'application/pdf' => '.pdf',
            'image/jpeg' => '.jpg',
            'image/png' => '.png',
            'image/gif' => '.gif',
            'application/msword' => '.doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => '.docx',
        ];

        return $map[$mime] ?? '.bin';
    }
}