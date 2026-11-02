<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Course extends Model
{
    use HasFactory;

    public const ACCESS_FREE = 'free';
    public const ACCESS_PAID = 'paid';
    public const ACCESS_SUBSCRIPTION = 'subscription';

    public const LEVEL_BEGINNER = 'beginner';
    public const LEVEL_INTERMEDIATE = 'intermediate';
    public const LEVEL_ADVANCED = 'advanced';

    public const STATUS_DRAFT = 'draft';
    public const STATUS_ON_MODERATION = 'on_moderation';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'author_id', 'title', 'slug', 'description', 'cover_path',
        'access_type', 'price', 'level', 'format', 'status',
    ];

    protected function casts(): array
    {
        return ['price' => 'decimal:2'];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class)->orderBy('sort_order');
    }

    public function lessons(): HasManyThrough
    {
        return $this->hasManyThrough(Lesson::class, Section::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }
}