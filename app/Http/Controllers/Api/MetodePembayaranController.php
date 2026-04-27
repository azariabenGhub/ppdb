<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MetodePembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MetodePembayaranController extends Controller
{
    public function index()
    {
        return response()->json(MetodePembayaran::all());
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_bank' => 'nullable|string',
            'nomor_rekening' => 'nullable|string',
            'atas_nama' => 'nullable|string',
            'gambar_qris' => 'nullable|image|mimes:jpg,png|max:2048',
            'keterangan' => 'nullable|string',
        ]);
        if ($request->hasFile('gambar_qris')) {
            $data['gambar_qris'] = $request->file('gambar_qris')->store('metode_pembayaran', 'public');
        }
        $metode = MetodePembayaran::create($data);
        return response()->json($metode, 201);
    }

    public function update(Request $request, $id)
    {
        $metode = MetodePembayaran::findOrFail($id);

        $data = $request->validate([
            'nama_bank' => 'nullable|string|max:100',
            'nomor_rekening' => 'nullable|string|max:50',
            'atas_nama' => 'nullable|string|max:100',
            'gambar_qris' => 'nullable|image|mimes:jpg,png|max:2048',
            'keterangan' => 'nullable|string|max:500',
        ]);

        // Jika ada unggahan gambar baru, hapus yang lama dan simpan yang baru
        if ($request->hasFile('gambar_qris')) {
            if ($metode->gambar_qris && Storage::disk('public')->exists($metode->gambar_qris)) {
                Storage::disk('public')->delete($metode->gambar_qris);
            }
            $data['gambar_qris'] = $request->file('gambar_qris')->store('metode_pembayaran', 'public');
        }

        $metode->update($data);

        return response()->json($metode, 200);
    }

    public function destroy($id)
    {
        $metode = MetodePembayaran::findOrFail($id);

        // Hapus file gambar QRIS jika ada
        if ($metode->gambar_qris && Storage::disk('public')->exists($metode->gambar_qris)) {
            Storage::disk('public')->delete($metode->gambar_qris);
        }

        $metode->delete();

        return response()->json(['message' => 'Metode pembayaran berhasil dihapus.'], 200);
    }
}
