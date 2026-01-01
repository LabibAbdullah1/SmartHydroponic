<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlantingHistory extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    // Agar kolom tanggal otomatis jadi objek Carbon
    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];
}
