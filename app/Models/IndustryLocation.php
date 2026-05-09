<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IndustryLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'statistic_category_id',
        'name',
        'slug',
        'industry_sector',
        'province',
        'city',
        'latitude',
        'longitude',
        'workforce',
        'investment_value',
        'output_value',
        'status',
        'address',
        'notes',
        'is_dummy',
        'sort_order',
        'meta',
    ];

    protected $casts = [
        'is_dummy' => 'boolean',
        'investment_value' => 'float',
        'latitude' => 'float',
        'longitude' => 'float',
        'meta' => 'array',
        'output_value' => 'float',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(StatisticCategory::class, 'statistic_category_id');
    }
}
