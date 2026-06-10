<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use App\Models\TechnicalCategory;

class TechnicalArticle extends Model
{
    protected $fillable = [
        'technical_category_id',
        'created_by',
        'title',
        'slug',
        'summary',
        'content',
        'thumbnail_url',
        'status',
        'view_count',
        'published_at',

        'source_type',
        'source_name',
        'source_url',
        'is_verified',
        'verified_by',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'view_count' => 'integer',
            'published_at' => 'datetime',
            'is_verified' => 'boolean',
            'verified_at' => 'datetime',
        ];
    }

    public function category()
    {
        return $this->belongsTo(TechnicalCategory::class, 'technical_category_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function verifier()
    {
    return $this->belongsTo(User::class, 'verified_by');
    }   
}