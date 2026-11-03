<?php

namespace App\Http\Controllers\Author;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    /**
     * Список курсов текущего автора.
     */
    public function index()
    {
        $courses = Auth::user()
            ->authoredCourses()
            ->withCount(['sections', 'lessons'])
            ->latest()
            ->get();

        return view('author.courses.index', compact('courses'));
    }

    /**
     * Форма создания курса.
     */
    public function create()
    {
        return view('author.courses.create', ['course' => new Course()]);
    }

    /**
     * Сохранение нового курса.
     */
    public function store(Request $request)
    {
        $data = $this->validateCourse($request);

        $data['author_id'] = Auth::id();
        $data['slug'] = $this->uniqueSlug($data['title']);
        $data['status'] = Course::STATUS_DRAFT;

        // Цена имеет смысл только для платных курсов
        if ($data['access_type'] !== Course::ACCESS_PAID) {
            $data['price'] = null;
        }

        if ($request->hasFile('cover')) {
            $data['cover_path'] = $request->file('cover')->store('covers', 'public');
        }

        $course = Course::create($data);

        return redirect()
            ->route('author.courses.edit', $course)
            ->with('success', 'Курс создан! Теперь заполните структуру: секции и уроки.');
    }

    /**
     * Форма редактирования курса.
     */
    public function edit(Course $course)
    {
        $this->authorizeCourse($course);

        return view('author.courses.edit', compact('course'));
    }

    /**
     * Обновление курса.
     */
    public function update(Request $request, Course $course)
    {
        $this->authorizeCourse($course);

        $data = $this->validateCourse($request);

        if ($data['access_type'] !== Course::ACCESS_PAID) {
            $data['price'] = null;
        }

        if ($request->hasFile('cover')) {
            if ($course->cover_path) {
                Storage::disk('public')->delete($course->cover_path);
            }
            $data['cover_path'] = $request->file('cover')->store('covers', 'public');
        }

        $course->update($data);

        return redirect()
            ->route('author.courses.index')
            ->with('success', 'Изменения сохранены.');
    }

    /**
     * Удаление курса.
     */
    public function destroy(Course $course)
    {
        $this->authorizeCourse($course);

        if ($course->cover_path) {
            Storage::disk('public')->delete($course->cover_path);
        }

        $course->delete();

        return redirect()
            ->route('author.courses.index')
            ->with('success', 'Курс удалён.');
    }

    /*
    |--------------------------------------------------------------------------
    | Приватные хелперы
    |--------------------------------------------------------------------------
    */

    /**
     * Разрешаем действия только с собственными курсами автора.
     */
    private function authorizeCourse(Course $course): void
    {
        abort_unless($course->author_id === Auth::id(), 403);
    }

    /**
     * Валидация базовых данных курса с русскими сообщениями.
     */
    private function validateCourse(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'level' => ['required', 'in:beginner,intermediate,advanced'],
            'access_type' => ['required', 'in:free,paid,subscription'],
            'format' => ['required', 'in:video,text,practice,mixed'],
            'price' => [
                'nullable', 'numeric', 'min:0', 'max:999999',
                'required_if:access_type,paid',
                'prohibited_if:access_type,free',
                'prohibited_if:access_type,subscription',
            ],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'title.required' => 'Укажите название курса.',
            'title.string' => 'Название должно быть строкой.',
            'title.max' => 'Название не должно превышать 255 символов.',

            'description.string' => 'Описание должно быть строкой.',
            'description.max' => 'Описание не должно превышать 5000 символов.',

            'level.required' => 'Выберите уровень сложности.',
            'level.in' => 'Недопустимое значение уровня.',

            'access_type.required' => 'Выберите тип доступа к курсу.',
            'access_type.in' => 'Недопустимый тип доступа.',

            'format.required' => 'Выберите формат курса.',
            'format.in' => 'Недопустимый формат курса.',

            'price.numeric' => 'Цена должна быть числом.',
            'price.min' => 'Цена не может быть отрицательной.',
            'price.max' => 'Цена слишком высокая.',
            'price.required_if' => 'Для платного курса укажите цену.',
            'price.prohibited_if' => 'Цена указывается только для платных курсов.',

            'cover.image' => 'Файл должен быть изображением.',
            'cover.mimes' => 'Допустимые форматы: jpg, jpeg, png, webp.',
            'cover.max' => 'Размер изображения не должен превышать 2 МБ.',
        ]);
    }

    /**
     * Уникальный slug из названия (транслитерация + суффикс при коллизии).
     */
    private function uniqueSlug(string $title): string
    {
        $base = Str::slug($title) ?: 'course-'.time();
        $slug = $base;
        $i = 1;

        while (Course::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}