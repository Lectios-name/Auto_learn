<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonCompletion;
use App\Models\Section;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Главный дашборд: статистика по роли пользователя.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isAuthor()) {
            return view('dashboard', ['role' => 'author', 'stats' => $this->authorStats($user)]);
        }

        if ($user->isAdmin()) {
            return view('dashboard', ['role' => 'admin', 'stats' => $this->adminStats()]);
        }

        return view('dashboard', ['role' => 'student', 'stats' => $this->studentStats($user)]);
    }

    /**
     * Страница студента «Мои курсы» (переехала из Student\DashboardController).
     */
    public function myCourses(Request $request)
    {
        $enrollments = Enrollment::where('user_id', $request->user()->id)
            ->with(['course.author', 'course.tags'])
            ->latest('updated_at')
            ->get();

        return view('student.my-courses', compact('enrollments'));
    }

    /*
    |--------------------------------------------------------------------------
    | Статистика по ролям
    |--------------------------------------------------------------------------
    */

    private function studentStats(User $user): array
    {
        $enrollments = Enrollment::where('user_id', $user->id)
            ->with('course')
            ->get();

        return [
            'courses_in_progress' => $enrollments->where('progress_percent', '<', 100)->count(),
            'courses_completed' => $enrollments->where('progress_percent', '>=', 100)->count(),
            'lessons_completed' => LessonCompletion::where('user_id', $user->id)->count(),
            'avg_test_score' => null, // появится вместе с движком тестов (Этап 6)
            'enrollments' => $enrollments->sortByDesc('updated_at')->take(5),
        ];
    }

    private function authorStats(User $user): array
    {
        $courseIds = $user->authoredCourses()->pluck('id');
        $sectionIds = Section::whereIn('course_id', $courseIds)->pluck('id');

        return [
            'courses_count' => $user->authoredCourses()->count(),
            'published_count' => $user->authoredCourses()->where('status', Course::STATUS_PUBLISHED)->count(),
            'students_count' => Enrollment::whereIn('course_id', $courseIds)->distinct()->count('user_id'),
            'lessons_count' => Lesson::whereIn('section_id', $sectionIds)->count(),
            'avg_rating' => (float) $user->authoredCourses()->avg('rating_avg'),
            'revenue' => null, // появится вместе с ЮMoney (Этап 5)
            'courses' => $user->authoredCourses()->latest()->take(5)->get(),
        ];
    }

    private function adminStats(): array
    {
        return [
            'users_count' => User::count(),
            'courses_count' => Course::count(),
            'on_moderation' => Course::where('status', Course::STATUS_ON_MODERATION)->count(),
            'published' => Course::where('status', Course::STATUS_PUBLISHED)->count(),
        ];
    }
}