<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('weather_data', function (Blueprint $table) {
           $table->id();

        $table->foreignId('coffee_farm_id')
              ->constrained('coffee_farms')
              ->cascadeOnDelete();

        $table->date('weather_date');

        $table->decimal('temperature_min', 5, 2)->nullable();
        $table->decimal('temperature_max', 5, 2)->nullable();
        $table->decimal('temperature_current', 5, 2)->nullable();

        $table->unsignedTinyInteger('humidity')->nullable();

        $table->decimal('precipitation', 8, 2)->nullable();

        $table->unsignedTinyInteger('precipitation_probability')->nullable();

        $table->decimal('wind_speed', 8, 2)->nullable();

        $table->string('weather_code', 50)->nullable();

        $table->string('source', 100)->default('Open-Meteo');

        $table->timestamp('fetched_at')->nullable();

        $table->enum('data_type', [
            'forecast',
            'current',
            'historical'
        ])->default('forecast');

        $table->timestamps();

        $table->unique(
            ['coffee_farm_id', 'weather_date', 'data_type'],
            'weather_data_unique_farm_date_type'
        );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weather_data');
    }
};
