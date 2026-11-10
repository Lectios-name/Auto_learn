<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LessonComment extends Model
{
    protected $fillable = ['user_id', 'lesson_id', 'parent_id', 'content'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(LessonComment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(LessonComment::class, 'parent_id')->oldest();
    }

    /**
     * Только корневые комментарии (без parent_id) с подгруженными ответами.
     */
    public function scopeRootComments($query)
    {
        return $query->whereNull('parent_id')->with(['user', 'replies.user', 'replies.replies.user'])->oldest();
    }
}