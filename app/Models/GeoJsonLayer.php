<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeoJsonLayer extends Model
{
    use HasFactory;

    protected $fillable = [
        'statistic_category_id',
        'name',
        'slug',
        'source_file',
        'geojson',
        'style',
        'is_active',
        'notes',
        'uploaded_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'style' => 'array',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(StatisticCategory::class, 'statistic_category_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getGeojsonPayloadAttribute(): array
    {
        if (! filled($this->geojson)) {
            return [];
        }

        return json_decode($this->geojson, true) ?: [];
    }
}
