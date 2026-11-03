<?php

namespace App\Http\Controllers;

use App\Models\Course;

class CoursePageController extends Controller
{
    public function show(Course $course)
    {
        abort_unless($course->status === Course::STATUS_PUBLISHED, 404);

        $course->load(['author', 'tags', 'sections.lessons']);

        return view('courses.show', compact('course'));
    }
}