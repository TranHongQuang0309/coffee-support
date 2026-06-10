<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CoffeeFarm; 

class WeatherData extends Model
{
    protected $table = 'weather_data';

    protected $fillable = [
        'coffee_farm_id',
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

    protected function casts(): array
    {
        return [
            'weather_date' => 'date',
            'temperature_current' => 'decimal:2',
            'temperature_min' => 'decimal:2',
            'temperature_max' => 'decimal:2',
            'humidity_current' => 'integer',
            'precipitation_current' => 'decimal:2',
            'precipitation_sum' => 'decimal:2',
            'precipitation_probability_max' => 'integer',
            'wind_speed_current' => 'decimal:2',
            'wind_speed_max' => 'decimal:2',
            'weather_code_current' => 'integer',
            'weather_code_daily' => 'integer',
            'raw_data' => 'array',
            'fetched_at' => 'datetime',
        ];
    }

    public function coffeeFarm()
    {
        return $this->belongsTo(CoffeeFarm::class);
    }
}