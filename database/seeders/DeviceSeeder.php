<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Device;

class DeviceSeeder extends Seeder
{
    public function run(): void
    {
        // Kita gunakan firstOrCreate agar tidak duplikat saat dijalankan berulang
        // Kunci pencariannya adalah 'mqtt_topic'
        Device::firstOrCreate(
            ['mqtt_topic' => 'hidroponik/telemetry'],
            [
                'name' => 'ESP32 Utama',
                // Kolom description & status DIHAPUS karena tidak ada di migration
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
