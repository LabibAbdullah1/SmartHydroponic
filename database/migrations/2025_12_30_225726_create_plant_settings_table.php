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
        Schema::create('plant_settings', function (Blueprint $table) {
            $table->id();
            $table->string('plant_name')->default('Selada');
            $table->dateTime('started_at')->useCurrent();

            $table->integer('target_ppm')->default(800);
            $table->enum('tank_shape', ['kotak', 'tabung'])->default('kotak');

            // Dimensi
            $table->double('tank_length')->nullable();
            $table->double('tank_width')->nullable();
            $table->double('tank_diameter')->nullable();

            $table->double('tank_height_cm')->default(30);
            $table->double('nutrient_strength')->default(200);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plant_settings');
    }
};
