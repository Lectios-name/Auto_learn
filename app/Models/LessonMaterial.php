<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonMaterial extends Model
{
    use HasFactory;

    protected $fillable = ['lesson_id', 'title', 'path', 'size_bytes'];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }
}