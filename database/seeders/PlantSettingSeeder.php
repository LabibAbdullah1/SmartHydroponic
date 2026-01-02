<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PlantSetting;
use Illuminate\Support\Facades\DB;

class PlantSettingSeeder extends Seeder
{
    public function run(): void
    {
        // Hanya buat jika tabel kosong
        if (PlantSetting::count() == 0) {
            PlantSetting::create([
                'plant_name'        => 'Selada',
                'started_at'        => now(), // Waktu mulai tanam = sekarang

                'target_ppm'        => 800,
                'tank_shape'        => 'kotak', // Default bentuk kotak

                // Dimensi Default (cm)
                'tank_length'       => 50,
                'tank_width'        => 30,
                'tank_height_cm'    => 30,
                'tank_diameter'     => null, // Kosongkan karena bentuknya kotak

                'nutrient_strength' => 200,

                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        }
    }
}
