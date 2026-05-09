<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StatisticPeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'label',
        'year',
        'quarter',
        'sort_order',
    ];

    public function points(): HasMany
    {
        return $this->hasMany(StatisticPoint::class);
    }
}
