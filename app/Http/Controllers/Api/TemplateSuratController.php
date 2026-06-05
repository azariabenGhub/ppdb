<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TemplateSurat;
use App\Helpers\FileEncryptionHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TemplateSuratController extends Controller
{
    public function index()
    {
        return response()->json(TemplateSurat::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|in:Surat Pernyataan,Pakta Integritas',
            'file' => 'required|file|mimes:pdf,doc,docx|max:5120',
        ]);

        // Jika template dengan nama yang sama sudah ada, hapus file lama dan datanya agar tertimpa
        $existing = TemplateSurat::where('nama', $request->nama)->first();
        if ($existing) {
            if ($existing->file_path && Storage::disk('private')->exists($existing->file_path)) {
                Storage::disk('private')->delete($existing->file_path);
            }
            $existing->delete();
        }

        $file = $request->file('file');
        $originalExtension = $file->getClientOriginalExtension();
        $mimeType = $file->getMimeType();

        $path = FileEncryptionHelper::encryptAndStore($file, 'template_surat');

        $template = TemplateSurat::create([
            'nama' => $request->nama,
            'file_path' => $path,
            'file_extension' => $originalExtension,
            'mime_type' => $mimeType,
        ]);

        return response()->json($template, 201);
    }

    public function update(Request $request, $id)
    {
        $template = TemplateSurat::findOrFail($id);
        $request->validate([
            'nama' => 'sometimes|required|string|in:Surat Pernyataan,Pakta Integritas',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        if ($request->has('nama')) {
            $template->nama = $request->nama;
        }

        if ($request->hasFile('file')) {
            // Hapus file lama
            if ($template->file_path && Storage::disk('private')->exists($template->file_path)) {
                Storage::disk('private')->delete($template->file_path);
            }
            $file = $request->file('file');
            $path = FileEncryptionHelper::encryptAndStore($file, 'template_surat');
            $template->file_path = $path;
            $template->file_extension = $file->getClientOriginalExtension();
            $template->mime_type = $file->getMimeType();
        }

        $template->save();
        return response()->json($template);
    }

    public function destroy($id)
    {
        $template = TemplateSurat::findOrFail($id);
        if ($template->file_path && Storage::disk('private')->exists($template->file_path)) {
            Storage::disk('private')->delete($template->file_path);
        }
        $template->delete();
        return response()->json(['message' => 'Template dihapus.']);
    }

    public function download($id)
    {
        $template = TemplateSurat::findOrFail($id);
        $content = FileEncryptionHelper::getDecryptedContent($template->file_path);
        if (!$content) {
            return response()->json(['message' => 'File tidak ditemukan.'], 404);
        }

        // Gunakan ekstensi yang disimpan, fallback ke ekstensi dari mime type
        $extension = $template->file_extension;
        if (!$extension) {
            // Deteksi dari mime type (fallback)
            $mimeMap = [
                'application/pdf' => 'pdf',
                'application/msword' => 'doc',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            ];
            $mime = $template->mime_type;
            $extension = $mimeMap[$mime] ?? 'bin';
        }

        $filename = $template->nama . '.' . $extension;
        $mimeType = $template->mime_type ?? 'application/octet-stream';

        return response($content)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}