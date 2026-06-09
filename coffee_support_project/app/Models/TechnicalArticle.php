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
    ];

    protected function casts(): array
    {
        return [
            'view_count' => 'integer',
            'published_at' => 'datetime',
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
}