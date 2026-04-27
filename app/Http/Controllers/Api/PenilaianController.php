<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Penilaian;
use App\Models\SeleksiTes;
use Illuminate\Http\Request;

class PenilaianController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'id_pendaftar' => 'required|exists:users,id',
            'kemampuan_membaca' => 'required|string',
            'kemampuan_menulis' => 'required|string',
            'kemampuan_berhitung' => 'required|string',
            'baca_alquran' => 'nullable|string',
            'catatan' => 'nullable|string',
            'kelulusan_tes' => 'required|in:lulus,tidak_lulus',
        ]);

        $seleksi = SeleksiTes::where('id_pendaftar', $request->id_pendaftar)->first();
        if (!$seleksi)
            return response()->json(['message' => 'Pendaftar belum dijadwalkan'], 422);
        if ($seleksi->id_penilaian)
            return response()->json(['message' => 'Sudah dinilai'], 422);

        $penilaian = Penilaian::create([
            'id_penilai' => $request->user()->id,
            'id_pendaftar' => $request->id_pendaftar,
            'kemampuan_membaca' => $request->kemampuan_membaca,
            'kemampuan_menulis' => $request->kemampuan_menulis,
            'kemampuan_berhitung' => $request->kemampuan_berhitung,
            'baca_alquran' => $request->baca_alquran,
            'catatan' => $request->catatan,
        ]);

        $seleksi->update([
            'id_penilaian' => $penilaian->id_nilai,
            'kelulusan_tes' => $request->kelulusan_tes,
        ]);

        return response()->json(['message' => 'Penilaian berhasil disimpan', 'data' => $penilaian], 201);
    }

    public function index()
    {
        // Lihat data penilaian (untuk staf)
        $list = Penilaian::with('pendaftar:id,name', 'penilai:id,name', 'seleksiTes')->get();
        return response()->json($list);
    }
}
