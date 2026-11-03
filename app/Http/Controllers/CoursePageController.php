<?php

namespace App\Http\Controllers;
use App\Models\Enrollment;
use App\Models\Course;
use Illuminate\Http\Request;

class CoursePageController extends Controller
{
    public function show(Course $course, Request $request)
    {
        abort_unless($course->status === Course::STATUS_PUBLISHED, 404);

        $course->load(['author', 'tags', 'sections.lessons', 'reviews.user']);

        $enrollment = $request->user()
            ? Enrollment::where('user_id', $request->user()->id)
                ->where('course_id', $course->id)
                ->first()
            : null;

        return view('courses.show', compact('course', 'enrollment'));
    }
}