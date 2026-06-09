<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\User;

class MarketPrice extends Model
{
    protected $fillable = [
        'created_by',
        'market_scope',
        'province',
        'coffee_type',
        'price',
        'unit',
        'price_date',
        'source',
        'source_url',
        'source_type',
        'fetched_at',
        'note',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'price_date' => 'date',
            'fetched_at' => 'datetime',
        ];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}