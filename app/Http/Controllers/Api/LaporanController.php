<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
    public function exportExcel()
    {
        return Excel::download(new PendaftarLulusExport(), 'pendaftar_lulus.xlsx');
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