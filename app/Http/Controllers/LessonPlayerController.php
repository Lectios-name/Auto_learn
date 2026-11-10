<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonComment;
use App\Models\LessonCompletion;
use App\Models\LessonNote;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LessonPlayerController extends Controller
{
    /**
     * Плеер урока: видео/текст + заметки + комментарии.
     */
    public function show(Request $request, Lesson $lesson)
    {
        $user = $request->user();

        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $lesson->section->course_id)
            ->first();

        // Доступ: зачислен ИЛИ урок открытый (free preview)
        abort_unless($enrollment || $lesson->is_free_preview, 403);

        $lesson->load(['section.course', 'materials']);

        // Упорядоченный список всех уроков курса (секции и уроки отсортированы)
        $ordered = $lesson->section->course->sections
            ->flatMap(fn($section) => $section->lessons)
            ->values();

        $index = $ordered->search(fn($item) => $item->id === $lesson->id);

        $prevLesson = $index > 0 ? $ordered[$index - 1] : null;
        $nextLesson = ($index !== false && $index < $ordered->count() - 1)
            ? $ordered[$index + 1]
            : null;

        $note = LessonNote::where('user_id', $user->id)
            ->where('lesson_id', $lesson->id)
            ->first();

        $comments = $lesson->comments()->rootComments()->get();

        $isCompleted = LessonCompletion::where('user_id', $user->id)
            ->where('lesson_id', $lesson->id)
            ->exists();

        return view('lessons.player', compact(
            'lesson',
            'enrollment',
            'note',
            'comments',
            'isCompleted',
            'prevLesson',
            'nextLesson'
        ));
    }

    /**
     * Отметить урок завершённым (AJAX или обычный POST).
     */
    public function complete(Request $request, Lesson $lesson): RedirectResponse
    {
        $user = $request->user();

        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $lesson->section->course_id)
            ->firstOrFail();

        LessonCompletion::firstOrCreate([
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
            'course_id' => $lesson->section->course_id,
        ], [
            'completed_at' => now(),
        ]);

        $enrollment->recalculateProgress();
        $enrollment->update(['last_lesson_id' => $lesson->id]);

        if ($enrollment->fresh()->progress_percent >= 100) {
            return redirect()
                ->route('courses.show', $lesson->section->course)
                ->with('success', '🎉 Поздравляем! Курс «' . $lesson->section->course->title . '» полностью пройден.');
        }

        return back()->with('success', 'Урок завершён! Прогресс обновлён.');
    }

    /**
     * Снять отметку о завершении.
     */
    public function uncomplete(Request $request, Lesson $lesson): RedirectResponse
    {
        $user = $request->user();

        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $lesson->section->course_id)
            ->firstOrFail();

        LessonCompletion::where('user_id', $user->id)
            ->where('lesson_id', $lesson->id)
            ->delete();

        $enrollment->recalculateProgress();

        return back()->with('success', 'Отметка о завершении снята.');
    }

    /**
     * Сохранить/обновить личную заметку к уроку.
     */
    public function saveNote(Request $request, Lesson $lesson): RedirectResponse
    {
        $data = $request->validate([
            'content' => ['required', 'string', 'max:5000'],
        ], [
            'content.required' => 'Заметка не может быть пустой.',
            'content.max' => 'Заметка не должна превышать 5000 символов.',
        ]);

        LessonNote::updateOrCreate(
            ['user_id' => $request->user()->id, 'lesson_id' => $lesson->id],
            $data
        );

        return back()->with('success', 'Заметка сохранена.');
    }

    /**
     * Добавить комментарий или ответ.
     */
    public function storeComment(Request $request, Lesson $lesson): RedirectResponse
    {
        $data = $request->validate([
            'content' => ['required', 'string', 'max:2000'],
            'parent_id' => ['nullable', 'exists:lesson_comments,id'],
        ], [
            'content.required' => 'Комментарий не может быть пустым.',
            'content.max' => 'Комментарий не должен превышать 2000 символов.',
        ]);

        $lesson->comments()->create([
            'user_id' => $request->user()->id,
            'content' => $data['content'],
            'parent_id' => $data['parent_id'] ?? null,
        ]);

        return back()->with('success', 'Комментарий добавлен.');
    }
}