<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class FileEncryptionHelper
{
    /**
     * Proses file: semua gambar dikonversi ke JPEG, PDF/DOC dikompres dengan gzcompress.
     *
     * @param UploadedFile $file
     * @param int $jpegQuality Kualitas JPEG (1-100)
     * @return string Konten yang sudah diproses
     */
    protected static function processFile(UploadedFile $file, int $jpegQuality = 50): string
    {
        $mime = $file->getMimeType();
        $originalContent = file_get_contents($file->getRealPath());

        // SEMUA GAMBAR (termasuk PNG, JPEG, WEBP, GIF) → konversi ke JPEG
        if (str_starts_with($mime, 'image/')) {
            return self::convertImageToJpeg($file, $jpegQuality);
        }

        // PDF atau dokumen Word → kompres dengan gzcompress
        if (
            in_array($mime, [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            ])
        ) {
            return self::compressWithGz($originalContent);
        }

        // File lain → tidak diproses
        return $originalContent;
    }

    /**
     * Konversi semua gambar (PNG, JPEG, WEBP, GIF) ke JPEG dengan background putih.
     *
     * @param UploadedFile $file
     * @param int $quality
     * @return string
     */
    protected static function convertImageToJpeg(UploadedFile $file, int $quality): string
    {
        if (!class_exists(ImageManager::class)) {
            Log::warning('Intervention Image tidak tersedia, konversi gambar ke JPEG dilewati.');
            return file_get_contents($file->getRealPath());
        }

        try {
            $manager = new ImageManager(new Driver());
            $image = $manager->read($file->getRealPath());

            // Untuk gambar yang mungkin memiliki transparansi (PNG, WEBP) atau GIF,
            // buat kanvas putih lalu tempatkan gambar di atasnya.
            // Untuk JPEG asli, ini tetap aman (warna putih tidak akan terlihat).
            $width = $image->width();
            $height = $image->height();
            $canvas = $manager->create($width, $height)->fill('#ffffff');
            $canvas->place($image, 'center');

            // Encode sebagai JPEG
            $encoded = $canvas->toJpeg($quality);
            return $encoded->toString();
        } catch (\Exception $e) {
            Log::error('Gagal konversi gambar ke JPEG: ' . $e->getMessage());
            return file_get_contents($file->getRealPath()); // fallback ke asli
        }
    }

    /**
     * Kompres data menggunakan gzcompress (level 9 untuk maksimal).
     *
     * @param string $data
     * @return string
     */
    protected static function compressWithGz(string $data): string
    {
        return gzcompress($data, 9);
    }

    /**
     * Dekompres data jika diperlukan (mencoba gzuncompress).
     *
     * @param string $data
     * @return string
     */
    protected static function decompressIfNeeded(string $data): string
    {
        $uncompressed = @gzuncompress($data);
        if ($uncompressed !== false) {
            return $uncompressed;
        }
        return $data;
    }

    /**
     * Enkripsi dan simpan file.
     *
     * @param UploadedFile $file
     * @param string $directory
     * @return string Path file terenkripsi
     */
    public static function encryptAndStore($file, $directory)
    {
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        // Proses file (konversi gambar ke JPEG, kompres PDF/DOC)
        $processedContent = self::processFile($file);

        // Enkripsi hasil proses
        $encryptedContent = Crypt::encryptString($processedContent);

        $fileName = $originalName . '_' . time() . '.enc';
        $path = $directory . '/' . $fileName;
        Storage::disk('private')->put($path, $encryptedContent);

        return $path;
    }

    /**
     * Enkripsi dan simpan file ke path yang ditentukan (tanpa ekstensi .enc akan ditambahkan)
     *
     * @param UploadedFile $file
     * @param string $fullPathWithoutExt Contoh: "daftar_ulang/2026/PPDB-2026-1-0001/akte_kelahiran"
     * @return string Path lengkap file tersimpan (dengan .enc)
     */
    public static function encryptAndStoreToPath(UploadedFile $file, string $fullPathWithoutExt): string
    {
        $processedContent = self::processFile($file);
        $encryptedContent = Crypt::encryptString($processedContent);
        $path = $fullPathWithoutExt . '.enc';
        Storage::disk('private')->put($path, $encryptedContent);
        return $path;
    }

    /**
     * Ambil dan dekripsi file, lalu dekompres jika perlu.
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
            $decrypted = Crypt::decryptString($encrypted);
            // Coba dekompres (jika data asli dikompres dengan gzcompress)
            return self::decompressIfNeeded($decrypted);
        } catch (\Exception $e) {
            Log::error('Gagal mendekripsi file: ' . $e->getMessage());
            return false;
        }
    }
}