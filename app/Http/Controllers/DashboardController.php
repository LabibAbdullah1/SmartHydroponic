<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Device;
use App\Models\Telemetry;
use App\Models\PlantSetting;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $setting = PlantSetting::first();

        // Auto-fix jika started_at kosong
        if ($setting && !$setting->started_at) {
            $setting->update(['started_at' => now()]);
        }

        // 1. Ambil Device
        $device = Device::where('mqtt_topic', 'hidroponik/telemetry')->first();

        // Safety Check
        if (!$setting) {
            return view('dashboard', [
                'device' => null,
                'labels' => [],
                'values' => [],
                'setting' => null,
                'stats' => null
            ]);
        }

        // 2. Ambil Data Grafik (20 data terakhir)
        $data = collect([]);
        if ($device) {
            $data = Telemetry::where('device_id', $device->id)
                ->orderBy('received_at', 'desc')
                ->take(20)
                ->get()
                ->sortBy('received_at');
        }

        $labels = $data->pluck('received_at')->map(fn($d) => $d->format('H:i:s'));
        $values = $data->pluck('tds_ppm');

        // 3. Logika Statistik Sederhana
        $stats = $this->calculateStats($device, $setting);

        return view('dashboard', compact('device', 'labels', 'values', 'setting', 'stats'));
    }

    public function getSensorData()
    {
        $device = Device::where('mqtt_topic', 'hidroponik/telemetry')->first();

        if (!$device) {
            return response()->json([
                'labels' => [],
                'ppm' => [],
                'temp' => [],
                'ka_message' => 'Menunggu koneksi...',
                'ka_status' => 'WAITING',
                'is_online' => false
            ]);
        }

        $data = Telemetry::where('device_id', $device->id)
            ->orderBy('received_at', 'desc')
            ->take(100)->get();

        $latest = $data->first();
        $sortedData = $data->sortBy('received_at');
        $treshold = 20;

        $isOnline = $latest && $latest->received_at->diffInSeconds(now()) < $treshold;

        return response()->json([
            'labels' => $sortedData->pluck('received_at')->map(fn($d) => $d->format('H:i:s')),
            'ppm'    => $sortedData->pluck('tds_ppm'),
            'temp'   => $sortedData->pluck('suhu'),
            'ka_message' => $latest ? $latest->ka_message : '...',
            'ka_status'  => $latest ? $latest->ka_status : 'WAITING',
            'is_online'  => $isOnline
        ]);
    }

    // Private method agar index() tidak penuh sesak
    private function calculateStats($device, $setting)
    {
        $stats = [
            'today_max_temp' => 0,
            'today_min_temp' => 0,
            'plant_max_temp' => 0,
            'plant_min_temp' => 0,
            'plant_max_ppm' => 0,
            'plant_start_date' => $setting->started_at ? $setting->started_at->format('d M Y') : '-',
            'plant_ages' => $setting->started_at
                ? (function () use ($setting) {
                    $totalMinutes = $setting->started_at->diffInMinutes(now());

                    $days = intdiv($totalMinutes, 1440);
                    $hours = intdiv($totalMinutes % 1440, 60);
                    $minutes = $totalMinutes % 60;

                    return "{$days} Hari {$hours} Jam {$minutes} Menit";
                })()
                : '0 Hari 0 Jam 0 Menit',
        ];

        if ($device) {
            // Harian
            $todayData = Telemetry::where('device_id', $device->id)->whereDate('received_at', Carbon::today());
            $stats['today_max_temp'] = $todayData->max('suhu') ?? 0;
            $stats['today_min_temp'] = $todayData->min('suhu') ?? 0;

            // Per Periode Tanam
            if ($setting->started_at) {
                $plantData = Telemetry::where('device_id', $device->id)
                    ->where('received_at', '>=', $setting->started_at);

                $stats['plant_max_temp'] = $plantData->max('suhu') ?? 0;
                $stats['plant_min_temp'] = $plantData->min('suhu') ?? 0;
                $stats['plant_max_ppm']  = $plantData->max('tds_ppm') ?? 0;
            }
        }
        return $stats;
    }
}
