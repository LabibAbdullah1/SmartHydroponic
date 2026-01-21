<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Telemetry;
use App\Models\PlantSetting;
use App\Models\PlantingHistory;

class PlantingHistoryController extends Controller
{
    public function index()
    {
        // Ambil data history diurutkan dari yang terbaru (Panen terakhir)
        $histories = PlantingHistory::orderBy('finished_at', 'desc')->get();

        return view('history', compact('histories'));
    }

    public function finishSession(Request $request)
    {
        $setting = PlantSetting::first();
        if (!$setting || !$setting->started_at) {
            return back()->with('error', 'Tidak ada sesi tanam yang aktif.');
        }

        // 1. Tentukan Range Waktu
        $startTime = $setting->started_at;
        $endTime   = now();

        // 2. Ambil Data
        $telemetries = Telemetry::whereBetween('received_at', [$startTime, $endTime])->get();

        if ($telemetries->isEmpty()) {
            // Jika kosong, cuma reset tanggal saja
            $setting->update(['started_at' => now()]);
            return back()->with('warning', 'Sesi direset, tapi tidak ada data sensor untuk dianalisis.');
        }

        // 3. LOGIKA KA (Analisis Kualitas Tanam)
        $avgPpm = $telemetries->avg('tds_ppm');

        // Hitung Akurasi (Toleransi +/- 150 dari target)
        $targetPpm = $setting->target_ppm;
        $lowerLimit = $targetPpm - 150;
        $upperLimit = $targetPpm + 150;

        $validData = $telemetries->filter(function ($t) use ($lowerLimit, $upperLimit) {
            return $t->tds_ppm >= $lowerLimit && $t->tds_ppm <= $upperLimit;
        })->count();

        $totalData = $telemetries->count();
        // Rumus Skor Persentase
        $score = ($totalData > 0) ? round(($validData / $totalData) * 100, 1) : 0;

        // 4. Simpan Rapor
        PlantingHistory::create([
            'plant_name'         => $setting->plant_name,
            'started_at'         => $startTime,
            'finished_at'        => $endTime,
            'max_temp'           => $telemetries->max('suhu'),
            'min_temp'           => $telemetries->min('suhu'),
            'avg_ppm'            => round($avgPpm, 0),
            'ppm_accuracy_score' => $score,
            'notes'              => "Panen {$setting->plant_name}. Akurasi Nutrisi: {$score}%"
        ]);

        // 5. Mulai Sesi Baru
        $setting->update(['started_at' => now()]);

        return back()->with('success', "🎉 Panen Berhasil! Skor Kualitas Nutrisi: {$score}%");
    }

}
