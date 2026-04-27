<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Formulir;
use App\Models\VerifikasiFormulir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class VerifikasiController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'id_formulir' => 'required|exists:formulirs,id',
            'hasil_verifikasi' => ['required', Rule::in(['diterima', 'ditolak'])],
            'catatan' => 'required_if:hasil_verifikasi,ditolak|nullable|string',
        ]);

        $formulir = Formulir::findOrFail($request->id_formulir);

        // Hanya formulir dengan status 'menunggu' yang bisa diverifikasi
        if ($formulir->status !== 'menunggu') {
            return response()->json(['message' => 'Formulir sudah diverifikasi sebelumnya.'], 422);
        }

        DB::beginTransaction();
        try {
            // Simpan verifikasi
            VerifikasiFormulir::create([
                'id_verifikator' => $request->user()->id,
                'id_formulir' => $formulir->id,
                'hasil_verifikasi' => $request->hasil_verifikasi,
                'catatan' => $request->catatan,
            ]);

            // Update status pendaftaran
            $formulir->status = $request->hasil_verifikasi;
            $formulir->save();

            DB::commit();
            return response()->json(['message' => 'Verifikasi berhasil.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal memverifikasi.'], 500);
        }
    }
}