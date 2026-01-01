#include <WiFi.h>
#include <PubSubClient.h>
#include <DHT.h>
#include <NewPing.h>

// --- KONFIGURASI WIFI & MQTT ---
const char *ssid = "NAMA_WIFI_ANDA";
const char *password = "PASSWORD_WIFI_ANDA";
const char *mqtt_server = "192.168.1.X"; // IP Laptop/Server Laravel

// --- KONFIGURASI PIN (Sesuai Dokumen Hal 4-5) ---
// Sensor TDS [cite: 60]
#define TDS_PIN 34

// Sensor Ultrasonic [cite: 67, 68]
#define TRIG_PIN 5
#define ECHO_PIN 18
#define MAX_DISTANCE 200 // Maksimum jarak baca (cm)

// Sensor DHT22 [cite: 72]
#define DHT_PIN 4
#define DHT_TYPE DHT22

// Inisialisasi Objek
WiFiClient espClient;
PubSubClient client(espClient);
DHT dht(DHT_PIN, DHT_TYPE);
NewPing sonar(TRIG_PIN, ECHO_PIN, MAX_DISTANCE);

void setup()
{
    Serial.begin(115200);
    dht.begin();

    // Setup WiFi
    WiFi.begin(ssid, password);
    while (WiFi.status() != WL_CONNECTED)
    {
        delay(500);
        Serial.print(".");
    }
    Serial.println("\nWiFi Connected");

    // Setup MQTT
    client.setServer(mqtt_server, 1883);
}

void reconnect()
{
    while (!client.connected())
    {
        if (client.connect("ESP32_Hydro_Client"))
        {
            Serial.println("MQTT Connected");
        }
        else
        {
            delay(5000);
        }
    }
}

float getTDS()
{
    // Pembacaan TDS Sederhana (Perlu Kalibrasi Hardware)
    int sensorValue = analogRead(TDS_PIN);
    float voltage = sensorValue * (3.3 / 4095.0);
    // Rumus estimasi (biasanya perlu disesuaikan dengan modul TDS spesifik)
    float tdsValue = (133.42 * voltage * voltage * voltage - 255.86 * voltage * voltage + 857.39 * voltage) * 0.5;
    return tdsValue;
}

void loop()
{
    if (!client.connected())
    {
        reconnect();
    }
    client.loop();

    // --- BACA SEMUA SENSOR ---

    // 1. Baca Jarak (Ultrasonic)
    // Menggunakan median filter (rata-rata 5x baca) agar stabil [cite: 28]
    unsigned int uS = sonar.ping_median(5);
    float distanceCM = uS / US_ROUNDTRIP_CM;

    // 2. Baca TDS [cite: 56]
    float tds = getTDS();

    // 3. Baca Suhu [cite: 69]
    float temp = dht.readTemperature();

    // Validasi data (Cek jika sensor error)
    if (isnan(temp))
        temp = 0;

    // --- PACKING JSON ---
    // Format: {"dist": 10.5, "tds": 800, "temp": 28.5}
    char msg[100];
    snprintf(msg, 75, "{\"dist\":%.2f, \"tds\":%.2f, \"temp\":%.2f}", distanceCM, tds, temp);

    Serial.print("Mengirim: ");
    Serial.println(msg);

    // Publish ke satu topik utama
    client.publish("hidroponik/telemetry", msg);

    delay(2000); // Kirim setiap 2 detik
}