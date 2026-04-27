<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SeleksiTes;
use App\Models\User;
use Illuminate\Http\Request;

class PenjadwalanController extends Controller
{
    public function eligible()
    {
        $users = User::where('role', 'pendaftar')
            ->whereHas('formulir', fn($q) => $q->where('status', 'diterima'))
            ->whereHas('buktiPembayaran', fn($q) => $q->where('jenis_pembayaran', 'formulir')->where('status', 'diterima'))
            ->whereDoesntHave('seleksiTes')
            ->get();

        return response()->json($users->map(fn($u) => [
            'id' => $u->id,
            'name' => $u->name,
            'email' => $u->email,
        ]));
    }

    // Buat jadwal
    public function store(Request $request)
    {
        $request->validate([
            'id_pendaftar' => 'required|exists:users,id',
            'jadwal_tes' => 'required|date_format:Y-m-d H:i:s',
        ]);

        $exists = SeleksiTes::where('id_pendaftar', $request->id_pendaftar)->exists();
        if ($exists)
            return response()->json(['message' => 'Pendaftar sudah dijadwalkan'], 422);

        $jadwal = SeleksiTes::create([
            'id_pendaftar' => $request->id_pendaftar,
            'id_penjadwal' => $request->user()->id,
            'jadwal_tes' => $request->jadwal_tes,
        ]);

        return response()->json(['message' => 'Jadwal berhasil dibuat', 'data' => $jadwal], 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'jadwal_tes' => 'required|date_format:Y-m-d H:i:s',
        ]);

        $seleksi = SeleksiTes::findOrFail($id);
        $seleksi->update([
            'jadwal_tes' => $request->jadwal_tes,
        ]);

        return response()->json(['message' => 'Jadwal berhasil diupdate', 'data' => $seleksi]);
    }

    // Lihat semua jadwal (untuk staf)
    public function index()
    {
        $list = SeleksiTes::with([
            'pendaftar:id,name',
            'penjadwal:id,name',
            'penilaian'
        ])->get();

        return response()->json($list);
    }
}
