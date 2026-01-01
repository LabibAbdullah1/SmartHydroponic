<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlantSetting extends Model
{
    protected $table = 'plant_settings';
    protected $guarded = ['id']; // Izinkan semua kolom diupdate kecuali ID

    protected $casts = [
        'target_ppm'     => 'integer',
        'tank_height_cm' => 'float',
        'tank_length'    => 'float', // Baru
        'tank_width'     => 'float', // Baru
        'tank_diameter'  => 'float', // Baru
        'last_alert_at'  => 'datetime',
    ];

    // Helper: Hitung Luas Alas secara otomatis
    // Ini akan mempermudah kita di Worker nanti
    public function getBaseAreaAttribute()
    {
        if ($this->tank_shape == 'tabung') {
            // Rumus Luas Lingkaran: π x r x r
            // Jari-jari (r) = diameter / 2
            $r = $this->tank_diameter / 2;
            return 3.14159 * $r * $r;
        } else {
            // Rumus Luas Persegi Panjang: Panjang x Lebar
            return $this->tank_length * $this->tank_width;
        }
    }
}
