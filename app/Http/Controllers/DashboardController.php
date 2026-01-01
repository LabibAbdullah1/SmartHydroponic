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
        // 1. Ambil Settingan (Untuk tahu kapan masa tanam dimulai)
        $setting = PlantSetting::first();

        // Safety check jika setting belum ada
        if (!$setting) {
            return view('dashboard', [
                'device' => null,
                'labels' => [],
                'values' => [],
                'setting' => null,
                'stats' => null
            ]);
        }

        // 2. Ambil Device
        $device = Device::where('mqtt_topic', 'hidroponik/telemetry')->first();

        // Data Grafik (Logic Lama)
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

        // --- 3. LOGIKA STATISTIK (FITUR BARU) ---
        // Kita siapkan array kosong dulu
        $stats = [
            'today_max_temp' => 0,
            'today_min_temp' => 0,
            'plant_max_temp' => 0,
            'plant_min_temp' => 0,
            'plant_max_ppm'  => 0,
            'plant_start_date' => $setting->updated_at->format('d M Y'), // Tgl Tanam
            'plant_age_days'   => (int)$setting->updated_at->diffInDays(now()), // Umur Tanaman
        ];

        if ($device) {
            // A. Statistik HARI INI
            $todayData = Telemetry::where('device_id', $device->id)
                ->whereDate('received_at', Carbon::today());

            $stats['today_max_temp'] = $todayData->max('suhu') ?? 0;
            $stats['today_min_temp'] = $todayData->min('suhu') ?? 0;

            // B. Statistik PERIODE TANAMAN SAAT INI
            // (Data diambil sejak terakhir kali tombol Simpan diklik)
            $plantData = Telemetry::where('device_id', $device->id)
                ->where('received_at', '>=', $setting->updated_at);

            $stats['plant_max_temp'] = $plantData->max('suhu') ?? 0;
            $stats['plant_min_temp'] = $plantData->min('suhu') ?? 0;
            $stats['plant_max_ppm']  = $plantData->max('tds_ppm') ?? 0;
        }

        return view('dashboard', compact('device', 'labels', 'values', 'setting', 'stats'));
    }

    public function getSensorData()
    {
        $device = Device::where('mqtt_topic', 'hidroponik/telemetry')->first();

        // Default response jika device/data tidak ada
        if (!$device) {
            return response()->json([
                'labels' => [],
                'ppm' => [],
                'temp' => [],
                'ka_message' => 'Menunggu koneksi alat...',
                'ka_status'  => 'WAITING',
                'is_online'  => false // <--- TAMBAHAN: Default Offline
            ]);
        }

        // Ambil data
        $data = Telemetry::where('device_id', $device->id)
            ->orderBy('received_at', 'desc')
            ->take(20)
            ->get();

        $latest = $data->first();
        $sortedData = $data->sortBy('received_at');

        // --- LOGIKA CEK ONLINE/OFFLINE ---
        $isOnline = false;
        if ($latest) {
            // Hitung selisih waktu sekarang dengan data terakhir
            // Jika selisih kurang dari 60 detik, anggap ONLINE
            if ($latest->received_at->diffInSeconds(now()) < 60) {
                $isOnline = true;
            }
        }

        return response()->json([
            'labels' => $sortedData->pluck('received_at')->map(fn($d) => $d->format('H:i:s')),
            'ppm'    => $sortedData->pluck('tds_ppm'),
            'temp'   => $sortedData->pluck('suhu'),
            'ka_message' => $latest ? $latest->ka_message : 'Menunggu data sensor...',
            'ka_status'  => $latest ? $latest->ka_status : 'WAITING',

            'is_online'  => $isOnline // <--- KIRIM STATUS KE VIEW
        ]);
    }

    // Fungsi untuk update setting yang tadi kita buat
    public function updateSettings(Request $request)
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

        $setting = PlantSetting::firstOrNew(['id' => 1]);

        $setting->plant_name     = $request->plant_name;
        $setting->target_ppm     = $request->target_ppm;
        $setting->tank_height_cm = $request->tank_height_cm;
        $setting->tank_shape     = $request->tank_shape;

        if ($request->tank_shape == 'kotak') {
            $setting->tank_length = $request->tank_length;
            $setting->tank_width  = $request->tank_width;
            $setting->tank_diameter = null;
        } else {
            $setting->tank_diameter = $request->tank_diameter;
            $setting->tank_length = null;
            $setting->tank_width  = null;
        }

        $setting->save();

        return back()->with('success', '✅ Pengaturan Tanaman & Tandon Berhasil Disimpan!');
    }
}
