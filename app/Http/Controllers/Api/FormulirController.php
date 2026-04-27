<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Formulir;
use Illuminate\Http\Request;
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
        $form = Formulir::with('user:id,name,email')->findOrFail($id);

        return response()->json([
            'data' => $form,
        ]);
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

        // Simpan data
        $data = $request->all();
        $data['user_id'] = $request->user()->id;

        $pendaftaran = Formulir::create($data);

        return response()->json([
            'message' => 'Pendaftaran berhasil',
            'data' => $pendaftaran
        ], 201);
    }
}
