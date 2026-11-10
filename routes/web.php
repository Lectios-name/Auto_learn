<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Author\CourseController as AuthorCourseController;
use App\Http\Controllers\Author\CourseBuilderController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CoursePageController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\LessonPlayerController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : view('home');
})->name('home');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog');
Route::get('/courses/{course:slug}', [CoursePageController::class, 'show'])->name('courses.show');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::post('/courses/{course}/enroll', [EnrollmentController::class, 'store'])->name('courses.enroll');
    Route::post('/courses/{course}/reviews', [ReviewController::class, 'store'])->name('courses.reviews.store');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

    Route::get('/lessons/{lesson}', [LessonPlayerController::class, 'show'])->name('lessons.show');
    Route::post('/lessons/{lesson}/complete', [LessonPlayerController::class, 'complete'])->name('lessons.complete');
    Route::post('/lessons/{lesson}/uncomplete', [LessonPlayerController::class, 'uncomplete'])->name('lessons.uncomplete');
    Route::post('/lessons/{lesson}/notes', [LessonPlayerController::class, 'saveNote'])->name('lessons.notes.save');
    Route::post('/lessons/{lesson}/comments', [LessonPlayerController::class, 'storeComment'])->name('lessons.comments.store');

    Route::get('/my-courses', [DashboardController::class, 'myCourses'])->middleware('auth')->name('student.my-courses');
});

Route::middleware(['auth', 'role:author'])
    ->prefix('author')
    ->name('author.')
    ->group(function () {
        Route::get('/courses', [AuthorCourseController::class, 'index'])->name('courses.index');
        Route::get('/courses/create', [AuthorCourseController::class, 'create'])->name('courses.create');
        Route::post('/courses', [AuthorCourseController::class, 'store'])->name('courses.store');
        Route::get('/courses/{course}/edit', [AuthorCourseController::class, 'edit'])->name('courses.edit');
        Route::put('/courses/{course}', [AuthorCourseController::class, 'update'])->name('courses.update');
        Route::delete('/courses/{course}', [AuthorCourseController::class, 'destroy'])->name('courses.destroy');

        // Конструктор структуры курса
        Route::get('/courses/{course}/builder', [CourseBuilderController::class, 'show'])->name('courses.builder');

        // Секции
        Route::post('/courses/{course}/sections', [CourseBuilderController::class, 'storeSection'])->name('builder.sections.store');
        Route::put('/sections/{section}', [CourseBuilderController::class, 'updateSection'])->name('builder.sections.update');
        Route::delete('/sections/{section}', [CourseBuilderController::class, 'destroySection'])->name('builder.sections.destroy');
        Route::post('/sections/{section}/move/{direction}', [CourseBuilderController::class, 'moveSection'])->where('direction', 'up|down')->name('builder.sections.move');

        // Уроки
        Route::post('/sections/{section}/lessons', [CourseBuilderController::class, 'storeLesson'])->name('builder.lessons.store');
        Route::put('/lessons/{lesson}', [CourseBuilderController::class, 'updateLesson'])->name('builder.lessons.update');
        Route::delete('/lessons/{lesson}', [CourseBuilderController::class, 'destroyLesson'])->name('builder.lessons.destroy');
        Route::post('/lessons/{lesson}/move/{direction}', [CourseBuilderController::class, 'moveLesson'])->where('direction', 'up|down')->name('builder.lessons.move');

        // Материалы
        Route::post('/lessons/{lesson}/materials', [CourseBuilderController::class, 'storeMaterial'])->name('builder.materials.store');
        Route::delete('/materials/{material}', [CourseBuilderController::class, 'destroyMaterial'])->name('builder.materials.destroy');
    });

Route::get('/67', function () {
    $count = (int) session('easter67', 0) + 1;
    session(['easter67' => $count]);

    return view('easter.67', ['count' => $count]);
})->name('easter67');

require __DIR__ . '/auth.php';
