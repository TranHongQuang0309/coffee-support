<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\DiagnosisRequest;

class Disease extends Model
{
    //
    protected $fillable = [
        'created_by',
        'name',
        'slug',
        'type',
        'symptoms',
        'causes',
        'prevention',
        'treatment',
        'severity',
        'image_url',
        'status',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function diagnosisRequests()
    {
    return $this->hasMany(DiagnosisRequest::class, 'predicted_disease_id');
    }
}
