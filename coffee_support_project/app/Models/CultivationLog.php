<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CultivationLog extends Model
{
    //
    protected $fillable = [
        'user_id',
        'coffee_farm_id',
        'log_date',
        'activity_type',
        'title',
        'description',
        'cost',
        'image_url',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function coffeeFarm()
    {
        return $this->belongsTo(CoffeeFarm::class);
    }
}
