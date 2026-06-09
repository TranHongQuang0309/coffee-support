<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use App\Models\CultivationLog;
use App\Models\DiagnosisRequest;
use App\Models\Question;
use App\Models\WeatherData;

class CoffeeFarm extends Model
{
    protected $fillable = [
        'user_id',
        'farm_name',
        'area',
        'coffee_type',
        'planting_year',
        'province',
        'district',
        'address',
        'latitude',
        'longitude',
        'description',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cultivationLogs()
    {
        return $this->hasMany(CultivationLog::class);
    }

    public function diagnosisRequests()
    {
        return $this->hasMany(DiagnosisRequest::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function weatherData()
    {
        return $this->hasMany(WeatherData::class);
    }
}