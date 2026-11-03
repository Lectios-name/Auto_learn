<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enrollment extends Model
{
    protected $fillable = [
        'user_id', 'course_id', 'progress_percent', 'last_lesson_id', 'completed_at',
    ];

    protected function casts(): array
    {
        return ['completed_at' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function lastLesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class, 'last_lesson_id');
    }

    /**
     * Пересчитывает прогресс по завершённым урокам (вызывается плеером).
     */
    public function recalculateProgress(): void
    {
        $total = $this->course->lessons()->count();

        if ($total === 0) {
            $this->update(['progress_percent' => 0]);

            return;
        }

        $done = LessonCompletion::where('user_id', $this->user_id)
            ->where('course_id', $this->course_id)
            ->count();

        $percent = (int) round($done / $total * 100);

        $this->update([
            'progress_percent' => $percent,
            'completed_at' => $percent === 100 ? ($this->completed_at ?? now()) : null,
        ]);
    }
}