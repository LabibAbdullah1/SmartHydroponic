@echo off
cd /d "%~dp0" && cd ..

echo ===================================================
echo   MEMULAI MODE SIMULASI (PHP + MQTT + PYTHON)
echo   Project IoT Monitoring Smart Hydroponic
echo ===================================================
echo Sedang memuat dashboard...
timeout /t 2 >nul

:: Jalankan perintah dalam satu baris panjang
wt -w 0 new-tab --title "Smart Hydroponic (testing)" -d . cmd /k "php -S 0.0.0.0:8000 -t public" ; split-pane -V --size 0.4 --title "MQTT" -d . cmd /k "php artisan mqtt:subscribe" ; split-pane -H --title "Sensor" -d .\testing cmd /k "python dummy_sensor.py"
