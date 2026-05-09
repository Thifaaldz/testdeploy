<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataSource extends Model
{
    use HasFactory;

    protected $fillable = [
        'statistic_category_id',
        'name',
        'slug',
        'source_type',
        'parser_key',
        'storage_disk',
        'file_path',
        'spreadsheet_url',
        'last_imported_at',
        'status',
        'last_error',
        'notes',
        'meta',
        'uploaded_by',
    ];

    protected $casts = [
        'last_imported_at' => 'datetime',
        'meta' => 'array',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(StatisticCategory::class, 'statistic_category_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
