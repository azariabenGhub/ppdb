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
    public function exportDinamis(Request $request)
    {
        $request->validate([
            'sumber' => 'required|in:formulir,daftar_ulang',
            'kolom' => 'required|array',
            'filter_nisn' => 'nullable|in:semua,sudah,belum',
        ]);

        $sumber = $request->sumber;
        $kolomTerpilih = $request->kolom;
        $filterNisn = $request->filter_nisn ?? 'semua';

        // Definisikan semua kolom yang tersedia beserta label dan cara mengaksesnya
        $kolomDefinition = $this->getKolomDefinition();

        // Validasi kolom terpilih
        $validKolom = array_filter($kolomTerpilih, function($k) use ($kolomDefinition) {
            return isset($kolomDefinition[$k]);
        });
        if (empty($validKolom)) {
            return response()->json(['message' => 'Tidak ada kolom valid yang dipilih'], 422);
        }

        // Ambil data berdasarkan sumber
        if ($sumber === 'formulir') {
            $data = $this->getDataFormulir($filterNisn);
        } else {
            $data = $this->getDataDaftarUlang($filterNisn);
        }

        // Mapping data menjadi array sesuai kolom terpilih
        $exportData = [];
        $headings = [];
        foreach ($validKolom as $kolom) {
            $headings[] = $kolomDefinition[$kolom]['label'];
        }

        foreach ($data as $item) {
            $row = [];
            foreach ($validKolom as $kolom) {
                $value = $this->extractValue($item, $kolom, $kolomDefinition[$kolom]['akses']);
                $row[] = $value;
            }
            $exportData[] = $row;
        }

        $export = new DinamisExport($exportData, $headings);
        $filename = 'export_' . $sumber . '_' . date('Ymd_His') . '.xlsx';
        return Excel::download($export, $filename);
    }

    private function getKolomDefinition()
    {
        return [
            // Kolom dari User
            'user_name' => ['label' => 'Nama Pendaftar', 'akses' => 'user.name'],
            'user_email' => ['label' => 'Email', 'akses' => 'user.email'],
            'no_pendaftaran' => ['label' => 'No. Induk Pendaftaran', 'akses' => 'no_pendaftaran'],
            
            // Kolom dari CalonSiswa (via formulir)
            'nama_siswa' => ['label' => 'Nama Siswa', 'akses' => 'nama_lengkap'],
            'tempat_lahir' => ['label' => 'Tempat Lahir', 'akses' => 'tempat_lahir'],
            'tanggal_lahir' => ['label' => 'Tanggal Lahir', 'akses' => 'tanggal_lahir'],
            'nik' => ['label' => 'NIK Siswa', 'akses' => 'nik'],
            'agama' => ['label' => 'Agama', 'akses' => 'agama'],
            'alamat' => ['label' => 'Alamat', 'akses' => 'alamat_lengkap'],
            'jenis_kelamin' => ['label' => 'Jenis Kelamin', 'akses' => 'jenis_kelamin'],
            'punya_nisn' => ['label' => 'Punya NISN', 'akses' => 'punya_nisn'],
            'nisn' => ['label' => 'NISN', 'akses' => 'nisn'],
            'pernah_tk' => ['label' => 'Pernah TK', 'akses' => 'pernah_tk'],
            'asal_tk' => ['label' => 'Asal TK', 'akses' => 'asal_tk'],
            'anak_ke' => ['label' => 'Anak ke-', 'akses' => 'anak_ke'],
            'jumlah_saudara' => ['label' => 'Jumlah Saudara', 'akses' => 'jumlah_saudara'],
            
            // Data orang tua/wali dari formulir
            'nama_ayah' => ['label' => 'Nama Ayah', 'akses' => 'nama_ayah'],
            'pekerjaan_ayah' => ['label' => 'Pekerjaan Ayah', 'akses' => 'pekerjaan_ayah'],
            'no_telp_ayah' => ['label' => 'No Telp Ayah', 'akses' => 'no_telp_ayah'],
            'nama_ibu' => ['label' => 'Nama Ibu', 'akses' => 'nama_ibu'],
            'pekerjaan_ibu' => ['label' => 'Pekerjaan Ibu', 'akses' => 'pekerjaan_ibu'],
            'no_telp_ibu' => ['label' => 'No Telp Ibu', 'akses' => 'no_telp_ibu'],
            'tipe_wali' => ['label' => 'Tipe Wali', 'akses' => 'tipe_wali'],
            'nama_wali' => ['label' => 'Nama Wali', 'akses' => 'nama_wali'],
            
            // Data gelombang
            'gelombang' => ['label' => 'Gelombang', 'akses' => 'gelombang.nomor_gelombang'],
            'biaya_formulir' => ['label' => 'Biaya Formulir', 'akses' => 'gelombang.biaya_formulir'],
            
            // Status
            'status_formulir' => ['label' => 'Status Formulir', 'akses' => 'status'],
            'kelulusan_tes' => ['label' => 'Kelulusan Tes', 'akses' => 'kelulusan_tes'],
            'status_daftar_ulang' => ['label' => 'Status Daftar Ulang', 'akses' => 'status_daftar_ulang'],
            
            // Untuk sumber daftar ulang
            'akte' => ['label' => 'Akte Kelahiran (file)', 'akses' => 'akte_kelahiran'],
            'ijazah' => ['label' => 'Ijazah TK', 'akses' => 'ijazah_tk'],
            'ktp_ortu' => ['label' => 'KTP Orang Tua', 'akses' => 'ktp_orang_tua'],
            'kk' => ['label' => 'Kartu Keluarga', 'akses' => 'kartu_keluarga'],
            'surat_pernyataan' => ['label' => 'Surat Pernyataan', 'akses' => 'surat_pernyataan'],
            'pakta_integritas' => ['label' => 'Pakta Integritas', 'akses' => 'surat_pakta_integritas'],
        ];
    }

    private function getDataFormulir($filterNisn)
    {
        $query = Formulir::with(['user', 'gelombang', 'calonSiswa', 'ayah', 'ibu', 'wali'])
            ->whereHas('user', function($q) {
                $q->where('role', 'pendaftar');
            });
        
        // Filter NISN
        if ($filterNisn === 'sudah') {
            $query->whereHas('calonSiswa', function($q) {
                $q->where('punya_nisn', true)->whereNotNull('nisn');
            });
        } elseif ($filterNisn === 'belum') {
            $query->whereHas('calonSiswa', function($q) {
                $q->where(function($sq) {
                    $sq->where('punya_nisn', false)->orWhereNull('nisn');
                });
            });
        }
        
        return $query->get();
    }

    private function getDataDaftarUlang($filterNisn)
    {
        $query = DaftarUlang::with(['user.formulir.calonSiswa', 'user.formulir.gelombang'])
            ->whereHas('user', function($q) {
                $q->where('role', 'pendaftar');
            });
        
        // Filter NISN berdasarkan relasi ke formulir -> calonSiswa
        if ($filterNisn === 'sudah') {
            $query->whereHas('user.formulir.calonSiswa', function($q) {
                $q->where('punya_nisn', true)->whereNotNull('nisn');
            });
        } elseif ($filterNisn === 'belum') {
            $query->whereHas('user.formulir.calonSiswa', function($q) {
                $q->where(function($sq) {
                    $sq->where('punya_nisn', false)->orWhereNull('nisn');
                });
            });
        }
        
        return $query->get();
    }

    private function extractValue($item, $kolomKey, $aksesPath)
    {
        // Pisahkan path dengan titik
        $parts = explode('.', $aksesPath);
        $value = $item;
        foreach ($parts as $part) {
            if (is_object($value) && isset($value->$part)) {
                $value = $value->$part;
            } elseif (is_array($value) && isset($value[$part])) {
                $value = $value[$part];
            } else {
                return '-';
            }
        }
        
        // Format khusus untuk kolom boolean
        if (in_array($kolomKey, ['punya_nisn', 'pernah_tk'])) {
            return $value ? 'Ya' : 'Tidak';
        }
        
        // Format tanggal
        if (in_array($kolomKey, ['tanggal_lahir']) && $value) {
            return date('d-m-Y', strtotime($value));
        }
        
        if (is_null($value) || $value === '') {
            return '-';
        }
        
        return $value;
    }

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