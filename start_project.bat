@echo off
cd /d "%~dp0"

wt -w 0 new-tab --title "My IoT Project" -d . cmd /k "php -S 0.0.0.0:8000 -t public" ; split-pane -V --size 0.5 --title "MQTT Listener" -d . cmd /k "php artisan mqtt:subscribe"
