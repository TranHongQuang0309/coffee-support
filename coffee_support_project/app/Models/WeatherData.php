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
        'temperature_min',
        'temperature_max',
        'temperature_current',
        'humidity',
        'precipitation',
        'precipitation_probability',
        'wind_speed',
        'weather_code',
        'source',
        'fetched_at',
        'data_type',
    ];

    protected function casts(): array
    {
        return [
            'weather_date' => 'date',
            'temperature_min' => 'decimal:2',
            'temperature_max' => 'decimal:2',
            'temperature_current' => 'decimal:2',
            'humidity' => 'decimal:2',
            'precipitation' => 'decimal:2',
            'precipitation_probability' => 'decimal:2',
            'wind_speed' => 'decimal:2',
            'fetched_at' => 'datetime',
        ];
    }

    public function coffeeFarm()
    {
        return $this->belongsTo(CoffeeFarm::class);
    }
}