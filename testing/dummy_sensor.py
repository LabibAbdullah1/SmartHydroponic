import paho.mqtt.client as mqtt
import time
import random
import json 

# --- KONFIGURASI PENTING ---
broker = "127.0.0.1"
topic = "hidroponik/telemetry" # <--- SUDAH BENAR DISINI

client = mqtt.Client()

print(f"Menghubungkan ke Broker {broker}...")

try:
    client.connect(broker, 1883, 60)
    print("✅ Python Terhubung! Mulai mengirim data ke: " + topic)
except Exception as e:
    print(f"❌ Gagal connect: {e}")
    exit()

# Variabel awal
current_dist = 10.0 
current_tds = 800.0 

try:
    while True:
        # Simulasi data berubah
        current_dist += random.uniform(0.1, 0.3)
        if current_dist > 30: current_dist = 5.0 

        current_tds += random.uniform(-10, 10)
        temp = round(random.uniform(28.0, 31.0), 1)

        # Packing JSON
        payload = {
            "dist": round(current_dist, 2),
            "tds": round(current_tds, 2),
            "temp": temp
        }
        
        json_payload = json.dumps(payload)

        # Kirim!
        client.publish(topic, json_payload)
        
        print(f"📤 Mengirim: {json_payload}")
        
        time.sleep(2) 

except KeyboardInterrupt:
    print("\nBerhenti.")