<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use App\Models\CoffeeFarm;
use App\Models\CultivationLog;
use App\Models\TechnicalArticle;
use App\Models\Disease;
use App\Models\DiagnosisRequest;
use App\Models\Question;
use App\Models\Answer;
use App\Models\MarketPrice;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'password',
        'role',
        'status',
        'avatar_url',
        'province',
        'district',
        'address',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'last_login_at' => 'datetime',
        ];
    }

    public function coffeeFarms()
    {
        return $this->hasMany(CoffeeFarm::class);
    }

    public function cultivationLogs()
    {
        return $this->hasMany(CultivationLog::class);
    }

    public function technicalArticles()
    {
        return $this->hasMany(TechnicalArticle::class, 'created_by');
    }

    public function diseases()
    {
        return $this->hasMany(Disease::class, 'created_by');
    }

    public function diagnosisRequests()
    {
        return $this->hasMany(DiagnosisRequest::class);
    }

    public function diagnosedRequests()
    {
        return $this->hasMany(DiagnosisRequest::class, 'diagnosed_by');
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function marketPrices()
    {
        return $this->hasMany(MarketPrice::class, 'created_by');
    }
}