<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Telemetry extends Model
{
    protected $table = 'telemetries';

    // IZINKAN SEMUA KOLOM DIISI
    protected $guarded = ['id'];

    public $timestamps = false;

    protected $casts = [
        'received_at' => 'datetime',
        // Casting float biar presisi
        'water_level_cm' => 'float',
        'volume_liter' => 'float',
        'tds_ppm' => 'float',
    ];
}
