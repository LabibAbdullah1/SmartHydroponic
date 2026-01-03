#!/bin/bash

# Ambil lokasi folder di mana file ini berada
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
# Definisikan PROJECT_ROOT (mundur satu folder dari script)
PROJECT_ROOT="$DIR/.."

# 1. Jalankan PHP Server di Tab Baru
osascript -e "tell application \"Terminal\" to do script \"cd '$DIR' && php -S 0.0.0.0:8000 -t public\""

# 2. Jalankan MQTT Subscribe di Tab Baru
osascript -e "tell application \"Terminal\" to do script \"cd '$DIR' && php artisan mqtt:subscribe\""

# 3. Jalankan Python Dummy Sensor di Tab Baru
osascript -e "tell application \"Terminal\" to do script \"cd '$DIR/testing' && python3 dummy_sensor.py\""
