#include <WiFi.h>
#include <PubSubClient.h>
#include <DHT.h>
#include <NewPing.h>

// ==========================================
// BAGIAN INI HARUS ANDA SESUAIKAN
// ==========================================
const char *ssid = "SmartHydroponic";
const char *password = "hydroponic";

// GANTI IP INI DENGAN HASIL 'ipconfig' DI LAPTOP ANDA SAAT INI
const char *mqtt_server = "192.168.213.212";
// ==========================================

// --- KONFIGURASI PIN ---
#define TDS_PIN 34
#define TRIG_PIN 5
#define ECHO_PIN 18
#define MAX_DISTANCE 200
#define DHT_PIN 4
#define DHT_TYPE DHT22

WiFiClient espClient;
PubSubClient client(espClient);
DHT dht(DHT_PIN, DHT_TYPE);
NewPing sonar(TRIG_PIN, ECHO_PIN, MAX_DISTANCE);

void setup() {
    Serial.begin(115200);
    dht.begin();

    // 1. KONEKSI WIFI
    Serial.print("\nMenghubungkan ke ");
    Serial.println(ssid);

    WiFi.mode(WIFI_STA);
    WiFi.begin(ssid, password);

    while (WiFi.status() != WL_CONNECTED) {
        delay(500);
        Serial.print(".");
    }

    Serial.println("\nWiFi Connected!");
    Serial.print("IP ESP32: ");
    Serial.println(WiFi.localIP());

    // 2. SETUP MQTT
    client.setServer(mqtt_server, 1883);
}

void reconnect() {
    // Loop sampai terhubung ke MQTT Broker (Laptop)
    while (!client.connected()) {
        Serial.print("Menghubungi Server MQTT (Laptop)...");

        // Buat Client ID unik agar tidak ditendang broker
        String clientId = "ESP32Client-";
        clientId += String(random(0xffff), HEX);

        if (client.connect(clientId.c_str())) {
            Serial.println("BERHASIL TERHUBUNG KE LAPTOP!");
        } else {
            Serial.print("Gagal, rc=");
            Serial.print(client.state());
            Serial.println(" (Coba lagi 5 detik...)");
            Serial.println("TIPS: Cek IP Laptop & Matikan Firewall Windows jika gagal terus.");
            delay(5000);
        }
    }
}

// Fungsi Baca TDS dengan Filter Noise (untuk tes tanpa sensor)
float getTDS() {
    int sensorValue = analogRead(TDS_PIN);
    if (sensorValue < 10) return 0.0; // Anggap 0 jika noise kecil
    float voltage = sensorValue * (3.3 / 4095.0);
    float tdsValue = (133.42 * voltage * voltage * voltage - 255.86 * voltage * voltage + 857.39 * voltage) * 0.5;
    return tdsValue;
}

void loop() {
    // Cek koneksi MQTT
    if (!client.connected()) {
        reconnect();
    }
    client.loop();

    // --- DATA DUMMY / BACA SENSOR ---
    // Kode ini aman dijalankan meski sensor belum dipasang

    unsigned int uS = sonar.ping_median(5);
    float distanceCM = (uS == 0) ? 0.0 : uS / US_ROUNDTRIP_CM;

    float tds = getTDS();

    float temp = dht.readTemperature();
    if (isnan(temp)) temp = 0.0;

    // --- KIRIM DATA ---
    char msg[128];
    snprintf(msg, sizeof(msg), "{\"dist\":%.2f, \"tds\":%.2f, \"temp\":%.2f}", distanceCM, tds, temp);

    Serial.print("Mengirim ke Laptop: ");
    Serial.println(msg);

    client.publish("hidroponik/telemetry", msg);

    delay(2000);
}
