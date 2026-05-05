<?php

namespace App\Http\Controllers\Api;

use App\Helpers\FileEncryptionHelper;
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
            $data['gambar_qris'] = FileEncryptionHelper::encryptAndStore($request->file('gambar_qris'), 'metode_pembayaran');
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

        if ($request->hasFile('gambar_qris')) {
            // Hapus file lama (terenkripsi)
            if ($metode->gambar_qris && Storage::disk('private')->exists($metode->gambar_qris)) {
                Storage::disk('private')->delete($metode->gambar_qris);
            }
            $data['gambar_qris'] = FileEncryptionHelper::encryptAndStore($request->file('gambar_qris'), 'metode_pembayaran');
        }

        $metode->update($data);
        return response()->json($metode, 200);
    }

    public function destroy($id)
    {
        $metode = MetodePembayaran::findOrFail($id);
        if ($metode->gambar_qris && Storage::disk('private')->exists($metode->gambar_qris)) {
            Storage::disk('private')->delete($metode->gambar_qris);
        }
        $metode->delete();
        return response()->json(['message' => 'Metode pembayaran berhasil dihapus.'], 200);
    }
}
