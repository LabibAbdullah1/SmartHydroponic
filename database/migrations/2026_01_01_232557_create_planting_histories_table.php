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
        Schema::create('planting_histories', function (Blueprint $table) {
            $table->id();
            $table->string('plant_name');
            $table->dateTime('started_at'); // Kapan mulai tanam
            $table->dateTime('finished_at'); // Kapan panen
            $table->float('max_temp')->default(0);
            $table->float('min_temp')->default(0);
            $table->float('avg_ppm')->default(0);
            $table->float('ppm_accuracy_score')->default(0); // SKOR KA (0-100%)
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planting_histories');
    }
};
