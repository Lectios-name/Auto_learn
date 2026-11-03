<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    /**
     * Бесплатное зачисление на курс.
     */
    public function store(Request $request, Course $course): RedirectResponse
    {
        abort_unless($course->status === Course::STATUS_PUBLISHED, 404);

        if ($course->access_type !== Course::ACCESS_FREE) {
            return back()->with('error', 'Платные курсы и курсы по подписке станут доступны после подключения оплаты (Этап 5).');
        }

        $enrollment = Enrollment::firstOrCreate([
            'user_id' => $request->user()->id,
            'course_id' => $course->id,
        ]);

        return $enrollment->wasRecentlyCreated
            ? back()->with('success', 'Вы зачислены на курс! Приятного обучения.')
            : back()->with('info', 'Вы уже зачислены на этот курс.');
    }
}