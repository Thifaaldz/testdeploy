<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StatisticCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'accent_color',
        'icon',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function dataSources(): HasMany
    {
        return $this->hasMany(DataSource::class);
    }

    public function geoJsonLayers(): HasMany
    {
        return $this->hasMany(GeoJsonLayer::class);
    }

    public function industryLocations(): HasMany
    {
        return $this->hasMany(IndustryLocation::class);
    }

    public function series(): HasMany
    {
        return $this->hasMany(StatisticSeries::class);
    }

    public function surveyProgresses(): HasMany
    {
        return $this->hasMany(SurveyProgress::class);
    }
}
