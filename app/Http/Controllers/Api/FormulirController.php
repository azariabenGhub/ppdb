<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Formulir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FormulirController extends Controller
{
    public function index()
    {
        $form = Formulir::with('user:id,name,email')->get();

        $data = $form->map(function ($item) {
            return [
                'id' => $item->id,
                'nama_pendaftar' => $item->user->name ?? 'Tanpa Nama',
                'email_pendaftar' => $item->user->email ?? '',
                'nama_lengkap' => $item->nama_lengkap,
                'tanggal_daftar' => $item->created_at->format('d-m-Y H:i'),
                'status' => $item->status ?? 'Baru',
            ];
        });

        return response()->json($data);
    }

    public function show($id)
    {
        $formulir = Formulir::with('user:id,name,email', 'verifikasi')->findOrFail($id);
        return response()->json(['data' => $formulir]);
    }

    public function store(Request $request)
    {
        $rules = [
            'nama_lengkap' => 'required|string|max:255',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'nik' => 'required|string|max:20',
            'agama' => 'required|string',
            'warga_negara' => 'required|string',
            'anak_ke' => 'nullable|integer|min:1',
            'jumlah_saudara' => 'nullable|integer|min:0',
            'alamat_lengkap' => 'required|string',
            'tipe_wali' => 'required|in:orang_tua,wali',
        ];

        if ($request->tipe_wali === 'orang_tua') {
            $rules = array_merge($rules, [
                'nama_ayah' => 'required|string|max:255',
                'pekerjaan_ayah' => 'required|string',
                'agama_ayah' => 'required|string',
                'pendidikan_ayah' => 'required|string',
                'no_ktp_ayah' => 'required|string|max:20',
                'penghasilan_ayah' => 'required|string',
                'no_telp_ayah' => 'required|string',
                'alamat_ayah' => 'required|string',
                'nama_ibu' => 'required|string|max:255',
                'pekerjaan_ibu' => 'required|string',
                'agama_ibu' => 'required|string',
                'pendidikan_ibu' => 'required|string',
                'no_ktp_ibu' => 'required|string|max:20',
                'penghasilan_ibu' => 'required|string',
                'no_telp_ibu' => 'required|string',
                'alamat_ibu' => 'required|string',
            ]);
        } else {
            $rules = array_merge($rules, [
                'nama_wali' => 'required|string|max:255',
                'pekerjaan_wali' => 'required|string',
                'agama_wali' => 'required|string',
                'pendidikan_wali' => 'required|string',
                'no_ktp_wali' => 'required|string|max:20',
                'penghasilan_wali' => 'required|string',
                'no_telp_wali' => 'required|string',
                'alamat_wali' => 'required|string',
            ]);
        }

        $rules['is_bukan_pindahan'] = 'required|boolean';
        if (!$request->is_bukan_pindahan) {
            $rules = array_merge($rules, [
                'asal_sekolah' => 'required|string',
                'no_ijazah' => 'required|string',
                'tahun_ijazah' => 'required|string|max:4',
                'diterima_kelas' => 'required|string',
                'pindah_dari' => 'required|string',
                'no_pindah' => 'required|string',
                'tanggal_pindah' => 'required|date',
            ]);
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = $request->user();
        $existing = Formulir::where('user_id', $user->id)->first();

        DB::beginTransaction();
        try {
            if ($existing) {
                // Hanya boleh update jika status 'menunggu' atau 'ditolak'
                if (!in_array($existing->status, ['menunggu', 'ditolak'])) {
                    return response()->json(['message' => 'Formulir tidak dapat diubah.'], 403);
                }
                // Hapus verifikasi lama (jika ada) agar bisa diverifikasi ulang
                $existing->verifikasi()->delete();
                // Update data
                $existing->update($request->all());
                // Set status kembali menunggu
                $existing->status = 'menunggu';
                $existing->save();
                $pendaftaran = $existing;
            } else {
                $data = $request->all();
                $data['user_id'] = $user->id;
                $pendaftaran = Formulir::create($data);
            }
            DB::commit();
            return response()->json(['message' => 'Formulir berhasil disimpan.', 'data' => $pendaftaran], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Terjadi kesalahan.', 'error' => $e->getMessage()], 500);
        }
    }

    public function myForm(Request $request)
    {
        $formulir = Formulir::with('verifikasi')->where('user_id', $request->user()->id)->first();
        if (!$formulir) {
            return response()->json(['data' => null, 'message' => 'Belum mengisi formulir.']);
        }
        return response()->json(['data' => $formulir]);
    }
}
