<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('weather_data', function (Blueprint $table) {
            if (!Schema::hasColumn('weather_data', 'weather_date')) {
                $table->date('weather_date')->after('coffee_farm_id');
            }

            if (!Schema::hasColumn('weather_data', 'temperature_current')) {
                $table->decimal('temperature_current', 5, 2)->nullable()->after('weather_date');
            }

            if (!Schema::hasColumn('weather_data', 'temperature_min')) {
                $table->decimal('temperature_min', 5, 2)->nullable()->after('temperature_current');
            }

            if (!Schema::hasColumn('weather_data', 'temperature_max')) {
                $table->decimal('temperature_max', 5, 2)->nullable()->after('temperature_min');
            }

            if (!Schema::hasColumn('weather_data', 'humidity_current')) {
                $table->unsignedTinyInteger('humidity_current')->nullable()->after('temperature_max');
            }

            if (!Schema::hasColumn('weather_data', 'precipitation_current')) {
                $table->decimal('precipitation_current', 6, 2)->nullable()->after('humidity_current');
            }

            if (!Schema::hasColumn('weather_data', 'precipitation_sum')) {
                $table->decimal('precipitation_sum', 6, 2)->nullable()->after('precipitation_current');
            }

            if (!Schema::hasColumn('weather_data', 'precipitation_probability_max')) {
                $table->unsignedTinyInteger('precipitation_probability_max')->nullable()->after('precipitation_sum');
            }

            if (!Schema::hasColumn('weather_data', 'wind_speed_current')) {
                $table->decimal('wind_speed_current', 6, 2)->nullable()->after('precipitation_probability_max');
            }

            if (!Schema::hasColumn('weather_data', 'wind_speed_max')) {
                $table->decimal('wind_speed_max', 6, 2)->nullable()->after('wind_speed_current');
            }

            if (!Schema::hasColumn('weather_data', 'weather_code_current')) {
                $table->integer('weather_code_current')->nullable()->after('wind_speed_max');
            }

            if (!Schema::hasColumn('weather_data', 'weather_code_daily')) {
                $table->integer('weather_code_daily')->nullable()->after('weather_code_current');
            }

            if (!Schema::hasColumn('weather_data', 'weather_text_current')) {
                $table->string('weather_text_current', 100)->nullable()->after('weather_code_daily');
            }

            if (!Schema::hasColumn('weather_data', 'weather_text_daily')) {
                $table->string('weather_text_daily', 100)->nullable()->after('weather_text_current');
            }

            if (!Schema::hasColumn('weather_data', 'source')) {
                $table->string('source', 100)->default('Open-Meteo')->after('weather_text_daily');
            }

            if (!Schema::hasColumn('weather_data', 'raw_data')) {
                $table->json('raw_data')->nullable()->after('source');
            }

            if (!Schema::hasColumn('weather_data', 'fetched_at')) {
                $table->timestamp('fetched_at')->nullable()->after('raw_data');
            }
        });
    }

    public function down(): void
    {
        Schema::table('weather_data', function (Blueprint $table) {
            $columns = [
                'weather_date',
                'temperature_current',
                'temperature_min',
                'temperature_max',
                'humidity_current',
                'precipitation_current',
                'precipitation_sum',
                'precipitation_probability_max',
                'wind_speed_current',
                'wind_speed_max',
                'weather_code_current',
                'weather_code_daily',
                'weather_text_current',
                'weather_text_daily',
                'source',
                'raw_data',
                'fetched_at',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('weather_data', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};