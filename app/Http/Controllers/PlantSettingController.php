<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PlantSetting;
use Illuminate\Validation\Rule; // Tambahkan ini untuk validasi canggih

class PlantSettingController extends Controller
{
    public function update(Request $request)
    {
        // 1. MANIPULASI REQUEST SEBELUM VALIDASI
        // Jika tabung_tidur, tinggi wadah = diameter. Kita isi manual biar lolos validasi.
        if ($request->tank_shape == 'tabung_tidur') {
            $request->merge(['tank_height_cm' => $request->tank_diameter]);
        }

        // 2. VALIDASI YANG SUDAH DIPERBAIKI
        $request->validate([
            'plant_name'     => 'required|string|max:50',
            'target_ppm'     => 'required|numeric|min:0',
            'tank_shape'     => 'required|in:kotak,tabung_tegak,tabung_tidur',
            'tank_height_cm' => 'required|numeric|min:1',

            // Lebar hanya wajib jika KOTAK
            'tank_width'     => 'nullable|required_if:tank_shape,kotak|numeric',

            // Panjang wajib jika KOTAK atau TABUNG TIDUR
            'tank_length'    => [
                'nullable',
                'numeric',
                Rule::requiredIf(fn() => in_array($request->tank_shape, ['kotak', 'tabung_tidur']))
            ],

            // Diameter wajib jika TABUNG TEGAK atau TABUNG TIDUR
            'tank_diameter'  => [
                'nullable',
                'numeric',
                Rule::requiredIf(fn() => in_array($request->tank_shape, ['tabung_tegak', 'tabung_tidur']))
            ],
        ]);

        $setting = PlantSetting::firstOrNew(['id' => 1]);

        if (!$setting->exists) {
            $setting->started_at = now();
        }

        // Simpan data umum
        $setting->plant_name     = $request->plant_name;
        $setting->target_ppm     = $request->target_ppm;
        $setting->tank_shape     = $request->tank_shape;

        // Simpan tinggi (untuk tabung tidur, nilainya sama dengan diameter)
        $setting->tank_height_cm = $request->tank_height_cm;

        // 3. LOGIKA PENYIMPANAN YANG BENAR (Perbaiki bagian ini)
        if ($request->tank_shape == 'kotak') {
            // KOTAK: Butuh P, L, T. Diameter Null.
            $setting->tank_length   = $request->tank_length;
            $setting->tank_width    = $request->tank_width;
            $setting->tank_diameter = null;
        } elseif ($request->tank_shape == 'tabung_tegak') {
            // TABUNG TEGAK: Butuh Diameter, T. Panjang & Lebar Null.
            $setting->tank_diameter = $request->tank_diameter;
            $setting->tank_length   = null;
            $setting->tank_width    = null;
        } elseif ($request->tank_shape == 'tabung_tidur') {
            // TABUNG TIDUR: Butuh Diameter, Panjang. Lebar Null.
            $setting->tank_diameter = $request->tank_diameter;
            $setting->tank_length   = $request->tank_length; // <--- JANGAN DI-NULL-KAN!
            $setting->tank_width    = null;
        }

        $setting->save();

        return back()->with('success', '✅ Pengaturan Tanaman & Tandon Berhasil Disimpan!');
    }
}
