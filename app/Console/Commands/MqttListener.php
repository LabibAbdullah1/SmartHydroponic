<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
use App\Models\Device;
use App\Models\PlantSetting;
use App\Models\Telemetry;


class MqttListener extends Command
{
    protected $signature = 'mqtt:subscribe';
    protected $description = 'Menjalankan worker untuk mendengarkan data sensor via MQTT';

    public function handle()
    {
        $server   = config('mqtt.host');
        $port     = config('mqtt.port');
        $topic    = config('mqtt.topic');
        $clientId = 'laravel-worker-' . uniqid();;

        $this->info("Mencoba terhubung ke MQTT Broker di $server:$port ...");

        try {
            $client = new MqttClient($server, $port, $clientId, MqttClient::MQTT_3_1);

            $settings = (new ConnectionSettings)
                ->setKeepAliveInterval(60)
                ->setLastWillTopic('client/status')
                ->setLastWillMessage('client disconnect')
                ->setLastWillQualityOfService(1);

            $client->connect($settings, true);
            $this->info("✅ Berhasil terhubung! Menunggu data...");

            $client->subscribe($topic, function ($topic, $message) {

                $payload = json_decode($message, true);

                if (isset($payload['dist'], $payload['tds'])) {

                    // --- 1. AMBIL DATA DEVICE (PERBAIKAN UTAMA) ---
                    // Kita cari ID device berdasarkan topik
                    $device = Device::where('mqtt_topic', $topic)->first();

                    if (!$device) {
                        $this->error("❌ Error: Device dengan topik '$topic' tidak ditemukan di database.");
                        return;
                    }
                    // ---------------------------------------------

                    $jarakSensor = (float) $payload['dist'];
                    $ppmAktual   = (float) $payload['tds'];

                    // --- 2. CEK SETTINGAN ---
                    $setting = PlantSetting::first();
                    if (!$setting) {
                        $this->error("❌ Error: Setting Tanaman Kosong! Harap isi di Web Dashboard.");
                        return;
                    }

                    // --- 3. LOGIKA KA (HITUNG VOLUME) ---
                    // Tinggi Air = Tinggi Total - Jarak Sensor
                    $tinggiAir = $setting->tank_height_cm - $jarakSensor;

                    // Jaga-jaga kalau air kosong (jarak sensor > tinggi tandon)
                    if ($tinggiAir < 0) $tinggiAir = 0;

                    $luasAlas = $setting->base_area;
                    if (!$luasAlas) {
                        $this->error("❌ Error: Luas Alas 0. Cek input dimensi di Web.");
                        return;
                    }

                    $volumeLiter = ($luasAlas * $tinggiAir) / 1000;

                    // --- 4. LOGIKA KA (HITUNG DOSIS) ---
                    $saranDosis = 0;
                    $pesanSaran = "Nutrisi Optimal";
                    $status = "OK";

                    if ($volumeLiter > 0) { // Hanya hitung dosis jika ada air
                        if ($ppmAktual < $setting->target_ppm) {
                            $gap = $setting->target_ppm - $ppmAktual;
                            $kekuatanNutrisi = $setting->nutrient_strength > 0 ? $setting->nutrient_strength : 200;

                            $saranDosis = ($gap / $kekuatanNutrisi) * $volumeLiter;
                            $saranDosis = round($saranDosis, 1);
                            $pesanSaran = "⚠️ KURANG NUTRISI! Tambahkan {$saranDosis}ml Nutrisi A & B";
                            $status = "WARNING";
                        } elseif ($ppmAktual > ($setting->target_ppm + 200)) {
                            $pesanSaran = "⚠️ KELEBIHAN NUTRISI! Tambahkan air baku.";
                            $status = "OVER";
                        }
                    } else {
                        $pesanSaran = "⚠️ AIR HABIS / SENSOR ERROR (Jarak: $jarakSensor cm)";
                        $status = "ERROR";
                    }

                    // --- 5. SIMPAN KE DATABASE (PERBAIKAN UTAMA) ---
                    try {
                        Telemetry::create([
                            'device_id'      => $device->id, // <--- INI YANG TADI HILANG
                            'water_level_cm' => $jarakSensor,
                            'volume_liter'   => $volumeLiter,
                            'tds_ppm'        => $ppmAktual,
                            'suhu'           => $payload['temp'] ?? 0,
                            'ka_status'      => $status,
                            'ka_message'     => $pesanSaran,
                            'received_at'    => now()
                        ]);
                        $this->info("✅ Sukses: Vol={$volumeLiter}L | Saran={$pesanSaran}");
                    } catch (\Exception $e) {
                        $this->error("❌ Gagal Simpan DB: " . $e->getMessage());
                    }
                }
            }, 0);

            $client->loop(true);
        } catch (\Exception $e) {
            $this->error("Error Koneksi: " . $e->getMessage());
        }
    }
}
