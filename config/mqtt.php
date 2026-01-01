<?php

return [
    'host'  => env('MQTT_HOST', '127.0.0.1'),
    'port'  => env('MQTT_PORT', 1883),
    'topic' => env('MQTT_TOPIC', 'hidroponik/telemetry'),
];
