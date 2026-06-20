<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function getBerandaSettings()
    {
        $alurSetting = Setting::find('beranda_alur');
        $kontakSetting = Setting::find('beranda_kontak');
        $alamatSetting = Setting::find('beranda_alamat');

        $alur = $alurSetting ? json_decode($alurSetting->value, true) : [];
        $kontak = $kontakSetting ? $kontakSetting->value : '';
        $alamat = $alamatSetting ? $alamatSetting->value : '';

        return response()->json([
            'alur' => $alur,
            'kontak' => $kontak,
            'alamat' => $alamat
        ]);
    }

    public function updateBerandaSettings(Request $request)
    {
        $request->validate([
            'alur' => 'required|array',
            'alur.*.step' => 'required|integer',
            'alur.*.title' => 'required|string|max:255',
            'alur.*.description' => 'required|string',
            'alur.*.date' => 'nullable|string',
            'kontak' => 'required|string',
            'alamat' => 'required|string',
        ]);

        Setting::updateOrCreate(
            ['key' => 'beranda_alur'],
            ['value' => json_encode($request->alur)]
        );

        Setting::updateOrCreate(
            ['key' => 'beranda_kontak'],
            ['value' => $request->kontak]
        );

        Setting::updateOrCreate(
            ['key' => 'beranda_alamat'],
            ['value' => $request->alamat]
        );

        return response()->json([
            'message' => 'Pengaturan beranda berhasil diperbarui'
        ]);
    }
}
