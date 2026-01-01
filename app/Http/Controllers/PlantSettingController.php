<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PlantSetting;

class PlantSettingController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'plant_name'      => 'required|string|max:50',
            'target_ppm'      => 'required|numeric|min:0',
            'tank_height_cm'  => 'required|numeric|min:1',
            'tank_shape'      => 'required|in:kotak,tabung',
            'tank_length'     => 'nullable|required_if:tank_shape,kotak|numeric',
            'tank_width'      => 'nullable|required_if:tank_shape,kotak|numeric',
            'tank_diameter'   => 'nullable|required_if:tank_shape,tabung|numeric',
        ]);

        // Gunakan firstOrNew dengan ID 1 agar data selalu update baris pertama
        $setting = PlantSetting::firstOrNew(['id' => 1]);

        // Jika data baru (belum ada di DB), set started_at
        if (!$setting->exists) {
            $setting->started_at = now();
        }

        $setting->plant_name     = $request->plant_name;
        $setting->target_ppm     = $request->target_ppm;
        $setting->tank_height_cm = $request->tank_height_cm;
        $setting->tank_shape     = $request->tank_shape;

        if ($request->tank_shape == 'kotak') {
            $setting->tank_length   = $request->tank_length;
            $setting->tank_width    = $request->tank_width;
            $setting->tank_diameter = null;
        } else {
            $setting->tank_diameter = $request->tank_diameter;
            $setting->tank_length   = null;
            $setting->tank_width    = null;
        }

        $setting->save();

        return back()->with('success', '✅ Pengaturan Tanaman & Tandon Berhasil Disimpan!');
    }
}
