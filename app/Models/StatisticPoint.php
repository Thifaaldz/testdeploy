<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StatisticPoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'statistic_series_id',
        'statistic_period_id',
        'label',
        'value',
        'sort_order',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
        'value' => 'float',
    ];

    public function period(): BelongsTo
    {
        return $this->belongsTo(StatisticPeriod::class, 'statistic_period_id');
    }

    public function series(): BelongsTo
    {
        return $this->belongsTo(StatisticSeries::class, 'statistic_series_id');
    }
}
