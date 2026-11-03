<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Course extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Константы (словари значений)
    |--------------------------------------------------------------------------
    */

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

    /**
     * Атрибуты, разрешённые для массового заполнения.
     */
    protected $fillable = [
        'author_id',
        'title',
        'slug',
        'description',
        'cover_path',
        'access_type',
        'price',
        'level',
        'format',
        'status',
        'rating_avg',
        'rating_count',
        'duration_minutes',
    ];

    /**
     * Приведение типов.
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'rating_avg' => 'decimal:2',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Связи
    |--------------------------------------------------------------------------
    */

    /**
     * Автор курса (преподаватель).
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Секции (модули) курса, отсортированные по порядку.
     */
    public function sections(): HasMany
    {
        return $this->hasMany(Section::class)->orderBy('sort_order');
    }

    /**
     * Все уроки курса через секции.
     */
    public function lessons(): HasManyThrough
    {
        return $this->hasManyThrough(Lesson::class, Section::class);
    }

    /**
     * Теги курса (для умного поиска в каталоге).
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Скоупы и хелперы
    |--------------------------------------------------------------------------
    */

    /**
     * Только опубликованные курсы (для витрины).
     */
    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    /**
     * Пересчитывает суммарную длительность курса по урокам.
     * Вызывается после создания/изменения/удаления уроков в конструкторе.
     */
    public function recalculateDuration(): void
    {
        $this->update(['duration_minutes' => (int) $this->lessons()->sum('duration_minutes')]);
    }
}