<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StatisticSeries extends Model
{
    use HasFactory;

    protected $fillable = [
        'statistic_category_id',
        'slug',
        'name',
        'group_key',
        'chart_type',
        'unit',
        'precision',
        'color',
        'description',
        'is_featured',
        'sort_order',
        'meta',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'meta' => 'array',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(StatisticCategory::class, 'statistic_category_id');
    }

    public function points(): HasMany
    {
        return $this->hasMany(StatisticPoint::class)->orderBy('sort_order')->orderBy('label');
    }
}
