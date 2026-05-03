<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class FileEncryptionHelper
{
    /**
     * Enkripsi dan simpan file.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $directory
     * @return string Path file terenkripsi
     */
    public static function encryptAndStore($file, $directory)
    {
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $encryptedContent = Crypt::encryptString(file_get_contents($file->getRealPath()));
        $fileName = $originalName . '_' . time() . '.enc';
        $path = $directory . '/' . $fileName;
        Storage::disk('private')->put($path, $encryptedContent);
        return $path;
    }

    /**
     * Ambil dan dekripsi file.
     *
     * @param string $path
     * @return string|false Content file asli, atau false jika gagal
     */
    public static function getDecryptedContent($path)
    {
        if (!Storage::disk('private')->exists($path)) {
            return false;
        }
        $encrypted = Storage::disk('private')->get($path);
        try {
            return Crypt::decryptString($encrypted);
        } catch (\Exception $e) {
            return false;
        }
    }
}