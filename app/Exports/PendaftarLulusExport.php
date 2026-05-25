<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PendaftarLulusExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    public function query()
    {
        return User::where('role', 'pendaftar')
            ->whereHas('seleksiTes', function($q) {
                $q->where('kelulusan_tes', 'lulus');
            })
            ->with(['formulir', 'seleksiTes']);
    }

    public function headings(): array
    {
        return [
            'No. Induk Pendaftaran',
            'Nama Pendaftar',
            'Email',
            'Nama Siswa',
            'Tempat Lahir',
            'Tanggal Lahir',
            'NIK',
            'Agama',
            'Alamat',
            'Status Formulir',
            'Kelulusan Tes',
            'Jadwal Tes',
            'Kemampuan Membaca',
            'Kemampuan Menulis',
            'Kemampuan Berhitung',
            'Baca Alquran',
        ];
    }

    public function map($user): array
    {
        $formulir = $user->formulir;
        $seleksi = $user->seleksiTes;
        $penilaian = $seleksi ? $seleksi->penilaian : null;

        return [
            $formulir->no_pendaftaran ?? '-',
            $user->name,
            $user->email,
            $formulir->nama_lengkap ?? '-',
            $formulir->tempat_lahir ?? '-',
            $formulir->tanggal_lahir ?? '-',
            $formulir->nik ?? '-',
            $formulir->agama ?? '-',
            $formulir->alamat_lengkap ?? '-',
            $formulir->status ?? '-',
            $seleksi->kelulusan_tes ?? '-',
            $seleksi->jadwal_tes ?? '-',
            $penilaian->kemampuan_membaca ?? '-',
            $penilaian->kemampuan_menulis ?? '-',
            $penilaian->kemampuan_berhitung ?? '-',
            $penilaian->baca_alquran ?? '-',
        ];
    }
}