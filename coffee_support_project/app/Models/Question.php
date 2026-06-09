<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use App\Models\CoffeeFarm;
use App\Models\Answer;

class Question extends Model
{
    protected $fillable = [
        'user_id',
        'coffee_farm_id',
        'title',
        'content',
        'image_url',
        'status',
        'view_count',
    ];

    protected function casts(): array
    {
        return [
            'view_count' => 'integer',
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

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }
}