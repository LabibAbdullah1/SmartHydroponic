<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('telemetries', function (Blueprint $table) {
            $table->id();

            // Data Sensor & Analisis KA
            $table->double('water_level_cm')->nullable();
            $table->double('volume_liter')->nullable();
            $table->double('tds_ppm')->nullable();
            $table->double('suhu')->default(0);

            $table->string('ka_status')->default('OK');
            $table->text('ka_message')->nullable();

            // Foreign Key ke tabel devices
            $table->unsignedBigInteger('device_id');
            $table->foreign('device_id')->references('id')->on('devices')->onDelete('cascade');

            // Kolom legacy (opsional, ada di SQL dump Anda)
            $table->float('value')->nullable();

            // Timestamp waktu terima data
            $table->timestamp('received_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('telemetries');
    }
};
