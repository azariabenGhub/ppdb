<?php
// app/Exports/PendaftarLulusExport.php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PendaftarLulusExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $selectedColumns;
    protected $filters;

    public function __construct(array $selectedColumns = [], array $filters = [])
    {
        $this->selectedColumns = $selectedColumns;
        $this->filters = $filters;
    }

    private function getAvailableColumns()
    {
        return [
            'no_pendaftaran' => 'No. Induk Pendaftaran',
            'nama_pendaftar' => 'Nama Pendaftar (User)',
            'email' => 'Email',
            'nama_siswa' => 'Nama Siswa',
            'tempat_lahir' => 'Tempat Lahir',
            'tanggal_lahir' => 'Tanggal Lahir',
            'jenis_kelamin' => 'Jenis Kelamin',
            'nik' => 'NIK',
            'agama' => 'Agama',
            'warga_negara' => 'Warga Negara',
            'anak_ke' => 'Anak ke-',
            'jumlah_saudara' => 'Jumlah Saudara',
            'alamat' => 'Alamat Lengkap',
            'pernah_tk' => 'Pernah TK',
            'asal_tk' => 'Asal TK',
            'punya_nisn' => 'Punya NISN',
            'nisn' => 'NISN',
            'nama_ayah' => 'Nama Ayah',
            'pekerjaan_ayah' => 'Pekerjaan Ayah',
            'agama_ayah' => 'Agama Ayah',
            'pendidikan_ayah' => 'Pendidikan Ayah',
            'nik_ayah' => 'NIK Ayah',
            'penghasilan_ayah' => 'Penghasilan Ayah',
            'no_telp_ayah' => 'No. Telp Ayah',
            'alamat_ayah' => 'Alamat Ayah',
            'nama_ibu' => 'Nama Ibu',
            'pekerjaan_ibu' => 'Pekerjaan Ibu',
            'agama_ibu' => 'Agama Ibu',
            'pendidikan_ibu' => 'Pendidikan Ibu',
            'nik_ibu' => 'NIK Ibu',
            'penghasilan_ibu' => 'Penghasilan Ibu',
            'no_telp_ibu' => 'No. Telp Ibu',
            'alamat_ibu' => 'Alamat Ibu',
            'tipe_wali' => 'Tipe Wali',
            'nama_wali' => 'Nama Wali',
            'pekerjaan_wali' => 'Pekerjaan Wali',
            'agama_wali' => 'Agama Wali',
            'pendidikan_wali' => 'Pendidikan Wali',
            'nik_wali' => 'NIK Wali',
            'penghasilan_wali' => 'Penghasilan Wali',
            'no_telp_wali' => 'No. Telp Wali',
            'alamat_wali' => 'Alamat Wali',
            'status_formulir' => 'Status Formulir',
            'kelulusan' => 'Kelulusan Tes',
            'jadwal_tes' => 'Jadwal Tes',
            'kemampuan_membaca' => 'Kemampuan Membaca',
            'kemampuan_menulis' => 'Kemampuan Menulis',
            'kemampuan_berhitung' => 'Kemampuan Berhitung',
            'baca_alquran' => 'Baca Alquran',
            'narahubung' => 'Narahubung (Daftar Ulang)',
            'alamat_domisili' => 'Alamat Domisili (Daftar Ulang)',
        ];
    }

    public function query()
    {
        $query = User::where('role', 'pendaftar')
            ->whereHas('seleksiTes', fn($q) => $q->where('kelulusan_tes', 'lulus'))
            ->with([
                'formulir.calonSiswa',
                'formulir.ayah',
                'formulir.ibu',
                'formulir.wali',
                'seleksiTes.penilaian',
                'daftarUlang.orangTua',
                'daftarUlang.wali'
            ]);

        // Filter tahun
        if (!empty($this->filters['tahun'])) {
            $query->whereYear('created_at', $this->filters['tahun']);
        }
        // Filter gelombang
        if (!empty($this->filters['gelombang'])) {
            $query->whereHas('formulir', fn($q) => $q->where('id_gelombang', $this->filters['gelombang']));
        }
        // Filter status formulir
        if (!empty($this->filters['status_formulir'])) {
            if ($this->filters['status_formulir'] === 'sudah') {
                $query->whereHas('formulir');
            } elseif ($this->filters['status_formulir'] === 'belum') {
                $query->whereDoesntHave('formulir');
            }
        }
        // Filter kelulusan (redundant karena sudah lulus, tapi untuk fleksibilitas)
        if (!empty($this->filters['kelulusan']) && $this->filters['kelulusan'] !== 'lulus') {
            // Jika filter memilih selain lulus, tidak akan ada hasil, biarkan saja
        }
        // Filter status daftar ulang
        if (!empty($this->filters['status_daftar_ulang'])) {
            $statusDu = $this->filters['status_daftar_ulang'];
            if ($statusDu === 'sudah') {
                $query->whereHas('daftarUlang');
            } elseif ($statusDu === 'belum') {
                $query->whereDoesntHave('daftarUlang');
            } else {
                $query->whereHas('daftarUlang', fn($q) => $q->where('status', $statusDu));
            }
        }
        // Filter NISN
        if (!empty($this->filters['nisn'])) {
            if ($this->filters['nisn'] === 'ya') {
                $query->whereHas('formulir.calonSiswa', fn($q) => $q->where('punya_nisn', true));
            } elseif ($this->filters['nisn'] === 'tidak') {
                $query->where(function($q) {
                    $q->whereDoesntHave('formulir.calonSiswa')
                      ->orWhereHas('formulir.calonSiswa', fn($sq) => $sq->where('punya_nisn', false));
                });
            }
        }
        // Filter search (nama, email, no_pendaftaran)
        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('formulir', fn($q2) => $q2->where('no_pendaftaran', 'like', "%{$search}%"));
            });
        }

        return $query;
    }

    public function headings(): array
    {
        $all = $this->getAvailableColumns();
        if (empty($this->selectedColumns)) {
            return array_values($all);
        }
        $headings = [];
        foreach ($this->selectedColumns as $col) {
            if (isset($all[$col])) {
                $headings[] = $all[$col];
            }
        }
        return $headings;
    }

    public function map($user): array
    {
        $formulir = $user->formulir;
        $calon = $formulir?->calonSiswa;
        $ayah = $formulir?->ayah;
        $ibu = $formulir?->ibu;
        $wali = $formulir?->wali;
        $seleksi = $user->seleksiTes;
        $penilaian = $seleksi?->penilaian;
        $daftarUlang = $user->daftarUlang;

        $narahubung = '-';
        $alamatDomisili = '-';
        if ($daftarUlang) {
            if ($daftarUlang->orangTua) {
                $narahubung = $daftarUlang->orangTua->narahubung ?? '-';
                $alamatDomisili = $daftarUlang->orangTua->alamat_domisili ?? '-';
            } elseif ($daftarUlang->wali) {
                $narahubung = $daftarUlang->wali->narahubung ?? '-';
                $alamatDomisili = $daftarUlang->wali->alamat_domisili ?? '-';
            }
        }

        $data = [
            'no_pendaftaran' => $formulir->no_pendaftaran ?? '-',
            'nama_pendaftar' => $user->name,
            'email' => $user->email,
            'nama_siswa' => $calon->nama_lengkap ?? '-',
            'tempat_lahir' => $calon->tempat_lahir ?? '-',
            'tanggal_lahir' => $calon->tanggal_lahir ?? '-',
            'jenis_kelamin' => $calon->jenis_kelamin ?? '-',
            'nik' => $calon->nik ?? '-',
            'agama' => $calon->agama ?? '-',
            'warga_negara' => $calon->warga_negara ?? '-',
            'anak_ke' => $calon->anak_ke ?? '-',
            'jumlah_saudara' => $calon->jumlah_saudara ?? '-',
            'alamat' => $calon->alamat_lengkap ?? '-',
            'pernah_tk' => $calon->pernah_tk ? 'Ya' : 'Tidak',
            'asal_tk' => $calon->asal_tk ?? '-',
            'punya_nisn' => $calon->punya_nisn ? 'Ya' : 'Tidak',
            'nisn' => $calon->nisn ?? '-',
            'nama_ayah' => $ayah->nama ?? '-',
            'pekerjaan_ayah' => $ayah->pekerjaan ?? '-',
            'agama_ayah' => $ayah->agama ?? '-',
            'pendidikan_ayah' => $ayah->pendidikan ?? '-',
            'nik_ayah' => $ayah->nik ?? '-',
            'penghasilan_ayah' => $ayah->penghasilan ?? '-',
            'no_telp_ayah' => $ayah->no_telp ?? '-',
            'alamat_ayah' => $ayah->alamat ?? '-',
            'nama_ibu' => $ibu->nama ?? '-',
            'pekerjaan_ibu' => $ibu->pekerjaan ?? '-',
            'agama_ibu' => $ibu->agama ?? '-',
            'pendidikan_ibu' => $ibu->pendidikan ?? '-',
            'nik_ibu' => $ibu->nik ?? '-',
            'penghasilan_ibu' => $ibu->penghasilan ?? '-',
            'no_telp_ibu' => $ibu->no_telp ?? '-',
            'alamat_ibu' => $ibu->alamat ?? '-',
            'tipe_wali' => $formulir->tipe_wali ?? '-',
            'nama_wali' => $wali->nama ?? '-',
            'pekerjaan_wali' => $wali->pekerjaan ?? '-',
            'agama_wali' => $wali->agama ?? '-',
            'pendidikan_wali' => $wali->pendidikan ?? '-',
            'nik_wali' => $wali->nik ?? '-',
            'penghasilan_wali' => $wali->penghasilan ?? '-',
            'no_telp_wali' => $wali->no_telp ?? '-',
            'alamat_wali' => $wali->alamat ?? '-',
            'status_formulir' => $formulir->status ?? '-',
            'kelulusan' => $seleksi->kelulusan_tes ?? '-',
            'jadwal_tes' => $seleksi->jadwal_tes ?? '-',
            'kemampuan_membaca' => $penilaian->kemampuan_membaca ?? '-',
            'kemampuan_menulis' => $penilaian->kemampuan_menulis ?? '-',
            'kemampuan_berhitung' => $penilaian->kemampuan_berhitung ?? '-',
            'baca_alquran' => $penilaian->baca_alquran ?? '-',
            'narahubung' => $narahubung,
            'alamat_domisili' => $alamatDomisili,
        ];

        if (empty($this->selectedColumns)) {
            return array_values($data);
        }
        $result = [];
        foreach ($this->selectedColumns as $col) {
            $result[] = $data[$col] ?? '-';
        }
        return $result;
    }
}