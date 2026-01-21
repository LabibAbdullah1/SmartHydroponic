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
        // Menggunakan config sesuai best practice yang sudah kita buat
        $server   = config('mqtt.host');
        $port     = config('mqtt.port');
        $topic    = config('mqtt.topic');
        $clientId = 'laravel-worker-' . uniqid();

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

                    // --- 1. AMBIL DATA DEVICE ---
                    $device = Device::where('mqtt_topic', $topic)->first();

                    if (!$device) {
                        $this->error("❌ Error: Device dengan topik '$topic' tidak ditemukan di database.");
                        return;
                    }

                    $jarakSensor = (float) $payload['dist'];
                    $ppmAktual   = (float) $payload['tds'];

                    // --- 2. CEK SETTINGAN ---
                    $setting = PlantSetting::first();
                    if (!$setting) {
                        $this->error("❌ Error: Setting Tanaman Kosong! Harap isi di Web Dashboard.");
                        return;
                    }

                    // --- 3. LOGIKA KA (HITUNG VOLUME) ---

                    // A. Tentukan Dimensi Dasar
                    $tinggiTotalWadah = 0;
                    if ($setting->tank_shape == 'tabung_tidur') {
                        // Jika tidur, tinggi wadah adalah DIAMETER nya
                        $tinggiTotalWadah = $setting->tank_diameter;
                    } else {
                        // Jika kotak/tegak, tinggi wadah adalah TINGGI ASLI
                        $tinggiTotalWadah = $setting->tank_height_cm;
                    }

                    // B. Hitung Tinggi Air Aktual
                    $tinggiAir = $tinggiTotalWadah - $jarakSensor;
                    if ($tinggiAir < 0) $tinggiAir = 0; // Filter minus

                    // C. Hitung Volume Berdasarkan Bentuk
                    $volumeLiter = 0;

                    // --- RUMUS 1: KOTAK ---
                    if ($setting->tank_shape == 'kotak') {
                        $luasAlas = $setting->tank_length * $setting->tank_width;
                        $volumeLiter = ($luasAlas * $tinggiAir) / 1000;

                        // --- RUMUS 2: TABUNG TEGAK (Silinder Berdiri) ---
                    } elseif ($setting->tank_shape == 'tabung_tegak') {
                        // Rumus: Pi x r x r x t
                        $jariJari = $setting->tank_diameter / 2;
                        $luasAlas = 3.14159 * ($jariJari * $jariJari);
                        $volumeLiter = ($luasAlas * $tinggiAir) / 1000;

                        // --- RUMUS 3: TABUNG TIDUR (Silinder Horizontal) ---
                    } elseif ($setting->tank_shape == 'tabung_tidur') {
                        // Rumus Tembereng Lingkaran
                        // V = Luas Penampang Air x Panjang Tabung

                        $r = $setting->tank_diameter / 2; // Jari-jari
                        $h = $tinggiAir;                  // Tinggi air
                        $L = $setting->tank_length;       // Panjang tabung ke samping

                        // Validasi fisika (Air tidak boleh melebihi diameter)
                        if ($h > (2 * $r)) $h = 2 * $r;

                        // Jika air kosong
                        if ($h <= 0) {
                            $volumeLiter = 0;
                        }
                        // Jika air penuh (sama dengan volume tabung full)
                        elseif ($h >= (2 * $r)) {
                            $volumeLiter = (3.14159 * $r * $r * $L) / 1000;
                        }
                        // Jika terisi sebagian (Rumus Kompleks)
                        else {
                            // Bagian 1: Luas Juring (Sektor)
                            // acos mengembalikan radian
                            $term1 = ($r * $r) * acos(($r - $h) / $r);

                            // Bagian 2: Luas Segitiga di atas air
                            $term2 = ($r - $h) * sqrt((2 * $r * $h) - ($h * $h));

                            // Luas Penampang Air = Bagian 1 - Bagian 2
                            $luasPenampang = $term1 - $term2;

                            // Volume = Luas x Panjang
                            $volumeLiter = ($luasPenampang * $L) / 1000;
                        }
                    }

                    // --- 4. LOGIKA KA (HITUNG DOSIS & PENGENCERAN) ---
                    $saranDosis = 0;
                    $pesanSaran = "Nutrisi Optimal";
                    $status = "OK";

                    if ($volumeLiter > 0) {

                        // KASUS A: KURANG NUTRISI (Tambah Pupuk)
                        if ($ppmAktual < $setting->target_ppm) {
                            $gap = $setting->target_ppm - $ppmAktual;
                            $kekuatanNutrisi = $setting->nutrient_strength > 0 ? $setting->nutrient_strength : 200;

                            $saranDosis = ($gap / $kekuatanNutrisi) * $volumeLiter;
                            $saranDosis = round($saranDosis, 1);

                            $pesanSaran = "KURANG NUTRISI! Tambahkan {$saranDosis} ml Nutrisi A & B";
                            $status = "WARNING";

                            // KASUS B: KELEBIHAN NUTRISI (Tambah Air Baku)
                            // Kita beri toleransi +100 dari target, jika lebih dari itu baru warning
                        } elseif ($ppmAktual > ($setting->target_ppm + 100)) {

                            // Rumus Pengenceran (C1.V1 = C2.V2)
                            // Volume Tambahan = (Vol Sekarang * (PPM Skrg - Target)) / Target

                            $literAirBaku = ($volumeLiter * ($ppmAktual - $setting->target_ppm)) / $setting->target_ppm;
                            $literAirBaku = round($literAirBaku, 1); // Bulatkan 1 angka belakang koma

                            $pesanSaran = "TERLALU PEKAT! Tambahkan ±{$literAirBaku} Liter Air Baku";
                            $status = "OVER";

                            // KASUS C: OPTIMAL
                        } else {
                            $pesanSaran = "Kondisi Nutrisi Optimal";
                            $status = "OPTIMAL";
                        }
                    } else {
                        $pesanSaran = "AIR HABIS / SENSOR ERROR (Jarak: $jarakSensor cm)";
                        $status = "ERROR";
                    }

                    // --- 5. SIMPAN KE DATABASE ---
                    try {
                        Telemetry::create([
                            'device_id'      => $device->id,
                            'water_level_cm' => $jarakSensor,
                            'volume_liter'   => $volumeLiter,
                            'tds_ppm'        => $ppmAktual,
                            'suhu'           => $payload['temp'] ?? 0,
                            'ka_status'      => $status,
                            'ka_message'     => $pesanSaran,
                            'received_at'    => now()
                        ]);
                        $this->info("✅ Sukses: Vol={$volumeLiter}L | PPM={$ppmAktual} | Saran: {$pesanSaran}");
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
