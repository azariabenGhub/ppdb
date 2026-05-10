<?php

namespace App\Http\Controllers\Api;

use App\Helpers\FileEncryptionHelper;
use App\Http\Controllers\Controller;
use App\Models\BuktiPembayaran;
use App\Models\DaftarUlang;
use App\Models\Kwitansi;
use App\Models\MetodePembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class EncryptedFileController extends Controller
{
    /**
     * Authentikasi manual dari Bearer token atau query parameter 'token'
     */
    private function authenticate(Request $request)
    {
        $token = $request->bearerToken() ?? $request->query('token');
        if (!$token) {
            return null;
        }
        $accessToken = PersonalAccessToken::findToken($token);
        if (!$accessToken) {
            return null;
        }
        $user = $accessToken->tokenable;
        Auth::setUser($user);
        return $user;
    }

    public function showBukti(Request $request, $id)
    {
        $user = $this->authenticate($request);
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $bukti = BuktiPembayaran::findOrFail($id);

        $isStaff = in_array($user->role, ['panitia', 'bendahara', 'kepala_sekolah']);
        $isOwner = ($user->id === $bukti->id_pendaftar);

        if (!($isStaff || $isOwner)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $content = FileEncryptionHelper::getDecryptedContent($bukti->bukti_pembayaran);
        if (!$content) {
            return response()->json(['message' => 'File not found or corrupted'], 404);
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_buffer($finfo, $content);
        finfo_close($finfo);

        return response($content)
            ->header('Content-Type', $mime)
            ->header('Cache-Control', 'no-cache, private');
    }

    public function showKwitansi(Request $request, $id)
    {
        $user = $this->authenticate($request);
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $kwitansi = Kwitansi::findOrFail($id);

        $isStaff = in_array($user->role, ['panitia', 'bendahara', 'kepala_sekolah']);
        $isOwner = ($user->id === $kwitansi->id_pendaftar);

        if (!($isStaff || $isOwner)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $content = FileEncryptionHelper::getDecryptedContent($kwitansi->kwitansi);
        if (!$content) {
            return response()->json(['message' => 'File not found or corrupted'], 404);
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_buffer($finfo, $content);
        finfo_close($finfo);

        return response($content)
            ->header('Content-Type', $mime)
            ->header('Cache-Control', 'no-cache, private');
    }

    public function showMetode(Request $request, $id)
    {
        $user = $this->authenticate($request);
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $metode = MetodePembayaran::findOrFail($id);
        if (!$metode->gambar_qris) {
            return response()->json(['message' => 'Gambar tidak tersedia'], 404);
        }

        $content = FileEncryptionHelper::getDecryptedContent($metode->gambar_qris);
        if (!$content) {
            return response()->json(['message' => 'File not found or corrupted'], 404);
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_buffer($finfo, $content);
        finfo_close($finfo);

        return response($content)
            ->header('Content-Type', $mime)
            ->header('Cache-Control', 'no-cache, private');
    }

    public function showDaftarUlangFile(Request $request, $id, $jenis)
    {
        $user = $this->authenticate($request);
        if (!$user)
            abort(401);
        $du = DaftarUlang::findOrFail($id);
        // Otorisasi: hanya staff atau pemilik
        if (!in_array($user->role, ['panitia', 'bendahara', 'kepala_sekolah']) && $user->id != $du->user_id)
            abort(403);
        $field = match ($jenis) {
            'akte' => 'akte_kelahiran',
            'ijazah' => 'ijazah_tk',
            'ktp' => 'ktp_orang_tua',
            'kk' => 'kartu_keluarga',
            'nisn' => 'nisn_file',
            'pernyataan' => 'surat_pernyataan',
            'pakta' => 'surat_pakta_integritas',
            default => abort(404)
        };
        $content = FileEncryptionHelper::getDecryptedContent($du->$field);
        if (!$content)
            abort(404);
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_buffer($finfo, $content);
        finfo_close($finfo);
        return response($content)->header('Content-Type', $mime);
    }
}