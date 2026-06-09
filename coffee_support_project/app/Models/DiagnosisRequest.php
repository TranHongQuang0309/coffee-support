<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use App\Models\CoffeeFarm;
use App\Models\Disease;

class DiagnosisRequest extends Model
{
    protected $fillable = [
        'user_id',
        'coffee_farm_id',
        'predicted_disease_id',
        'diagnosed_by',
        'image_url',
        'symptom_description',
        'confidence_score',
        'diagnosis_note',
        'recommendation',
        'status',
        'diagnosed_at',
    ];

    protected function casts(): array
    {
        return [
            'confidence_score' => 'decimal:2',
            'diagnosed_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function coffeeFarm()
    {
        return $this->belongsTo(CoffeeFarm::class);
    }

    public function predictedDisease()
    {
        return $this->belongsTo(Disease::class, 'predicted_disease_id');
    }

    public function diagnosedBy()
    {
        return $this->belongsTo(User::class, 'diagnosed_by');
    }
}