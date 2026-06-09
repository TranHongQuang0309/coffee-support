<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
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

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
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

}
