<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Gelombang;
use Illuminate\Http\Request;

class GelombangController extends Controller
{
    public function index()
    {
        return response()->json(Gelombang::orderBy('tahun', 'desc')->orderBy('nomor_gelombang')->get());
    }

    public function show($id)
    {
        $gelombang = Gelombang::findOrFail($id);
        return response()->json($gelombang);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomor_gelombang' => 'required|integer|min:1',
            'tahun' => 'required|integer|min:2000|max:2100',
            'periode_mulai' => 'required|date',
            'periode_selesai' => 'required|date|after:periode_mulai',
            'kuota' => 'required|integer|min:1',
            'biaya_formulir' => 'required|numeric|min:0',
            'biaya_daftar_ulang' => 'required|numeric|min:0',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $exists = Gelombang::where('nomor_gelombang', $request->nomor_gelombang)
            ->where('tahun', $request->tahun)
            ->exists();
        if ($exists) {
            return response()->json(['message' => 'Gelombang dengan nomor dan tahun tersebut sudah ada.'], 422);
        }

        $gelombang = Gelombang::create($request->all());
        return response()->json($gelombang, 201);
    }

    public function update(Request $request, $id)
    {
        $gelombang = Gelombang::findOrFail($id);
        $request->validate([
            'nomor_gelombang' => 'required|integer|min:1',
            'tahun' => 'required|integer|min:2000|max:2100',
            'periode_mulai' => 'required|date',
            'periode_selesai' => 'required|date|after:periode_mulai',
            'kuota' => 'required|integer|min:1',
            'biaya_formulir' => 'required|numeric|min:0',
            'biaya_daftar_ulang' => 'required|numeric|min:0',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $exists = Gelombang::where('nomor_gelombang', $request->nomor_gelombang)
            ->where('tahun', $request->tahun)
            ->where('id', '!=', $id)
            ->exists();
        if ($exists) {
            return response()->json(['message' => 'Gelombang dengan nomor dan tahun tersebut sudah ada.'], 422);
        }

        $gelombang->update($request->all());
        return response()->json($gelombang, 200);
    }

    public function destroy($id)
    {
        $gelombang = Gelombang::findOrFail($id);
        // Cek apakah sudah ada pendaftar yang menggunakan gelombang ini
        if ($gelombang->formulirs()->exists()) {
            return response()->json(['message' => 'Gelombang tidak bisa dihapus karena sudah digunakan pendaftar.'], 422);
        }
        $gelombang->delete();
        return response()->json(['message' => 'Gelombang dihapus.'], 200);
    }

    public function toggleStatus($id)
    {
        $gelombang = Gelombang::findOrFail($id);
        $gelombang->status = $gelombang->status === 'aktif' ? 'nonaktif' : 'aktif';
        $gelombang->save();
        return response()->json(['message' => 'Status berubah.', 'data' => $gelombang]);
    }
}