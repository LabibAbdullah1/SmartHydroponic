@echo off
cd /d "%~dp0"

:: Jalankan perintah dalam satu baris panjang
wt -w 0 new-tab --title "My IoT Project" -d . cmd /k "php -S 0.0.0.0:8000 -t public" ; split-pane -V --size 0.4 --title "MQTT" -d . cmd /k "php artisan mqtt:subscribe" ; split-pane -H --title "Sensor" -d .\testing cmd /k "python dummy_sensor.py"