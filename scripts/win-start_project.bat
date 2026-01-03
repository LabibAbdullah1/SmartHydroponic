@echo off
cd /d "%~dp0" && cd ..

echo ===================================================
echo   MEMULAI MODE PROJECT (PHP + MQTT )
echo   Project IoT Monitoring Smart Hydroponic
echo ===================================================
echo Sedang memuat dashboard...
timeout /t 2 >nul

wt -w 0 new-tab --title "Smart Hydroponic" -d . cmd /k "php -S 0.0.0.0:8000 -t public" ; split-pane -V --size 0.5 --title "MQTT Listener" -d . cmd /k "php artisan mqtt:subscribe"
