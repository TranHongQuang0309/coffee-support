<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\TechnicalArticle;
class TechnicalCategory extends Model
{
    //
     protected $fillable = [
        'name',
        'slug',
        'description',
        'status',
    ];

    public function articles()
    {
        return $this->hasMany(TechnicalArticle::class, 'technical_category_id');
    }
}
