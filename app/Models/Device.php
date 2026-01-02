<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;
    // Nama tabel di database (opsional jika sesuai konvensi, tapi kita tulis agar aman)
    protected $table = 'devices';

    // Kolom yang tidak boleh diisi massal (kita protect ID-nya)
    protected $guarded = ['id'];

    // Relasi: Satu Device memiliki BANYAK data Telemetry
    public function telemetries()
    {
        return $this->hasMany(Telemetry::class);
    }
}
