<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Helpers\FileEncryptionHelper;
use App\Models\BuktiPembayaran;
use App\Models\Kwitansi;
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
}