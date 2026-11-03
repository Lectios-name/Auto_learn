<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{

    public function store(Request $request, Course $course): RedirectResponse
    {
        $user = $request->user();

        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        abort_unless($enrollment, 403);

        if ($enrollment->progress_percent < 20) {
            return back()->with('error',
                'Оставить отзыв можно после прохождения минимум 20% курса. Ваш прогресс: '.$enrollment->progress_percent.'%.');
        }

        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'text' => ['nullable', 'string', 'max:2000'],
        ], [
            'rating.required' => 'Поставьте оценку от 1 до 5 звёзд.',
            'rating.min' => 'Минимальная оценка — 1 звезда.',
            'rating.max' => 'Максимальная оценка — 5 звёзд.',
            'text.max' => 'Отзыв не должен превышать 2000 символов.',
        ]);

        Review::updateOrCreate(
            ['user_id' => $user->id, 'course_id' => $course->id],
            $data
        );

        $course->recalculateRating();

        return back()->with('success', 'Спасибо! Ваш отзыв опубликован.');
    }

    /**
     * Удалить свой отзыв.
     */
    public function destroy(Request $request, Review $review): RedirectResponse
    {
        abort_unless($review->user_id === $request->user()->id, 403);

        $course = $review->course;

        $review->delete();
        $course->recalculateRating();

        return back()->with('success', 'Отзыв удалён.');
    }
}