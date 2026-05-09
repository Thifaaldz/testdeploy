<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SurveyProgress extends Model
{
    use HasFactory;

    protected $table = 'survey_progresses';

    protected $fillable = [
        'statistic_category_id',
        'activity_name',
        'target_awal',
        'selesai_dicacah',
        'sisa_target',
        'eligible',
        'sedang_dicacah',
        'condition_label',
        'sort_order',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(StatisticCategory::class, 'statistic_category_id');
    }

    public function getCompletionPercentageAttribute(): float
    {
        if ($this->target_awal <= 0) {
            return 0.0;
        }

        return round(($this->selesai_dicacah / $this->target_awal) * 100, 2);
    }
}
