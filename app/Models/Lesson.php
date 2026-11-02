<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lesson extends Model
{
    use HasFactory;

    public const TYPE_VIDEO = 'video';
    public const TYPE_TEXT = 'text';
    public const TYPE_TEST = 'test';
    public const TYPE_TASK = 'task';

    protected $fillable = [
        'section_id', 'title', 'type', 'content', 'video_url',
        'duration_minutes', 'is_free_preview', 'sort_order',
    ];

    protected function casts(): array
    {
        return ['is_free_preview' => 'boolean'];
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function materials(): HasMany
    {
        return $this->hasMany(LessonMaterial::class);
    }
}