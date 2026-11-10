<?php

namespace App\Http\Controllers\Author;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonMaterial;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CourseBuilderController extends Controller
{
    /**
     * Экран конструктора курса.
     */
    public function show(Course $course)
    {
        $this->authorizeCourse($course);

        $course->load(['sections.lessons.materials']);

        return view('author.courses.builder', compact('course'));
    }

    /*
    |--------------------------------------------------------------------------
    | Секции
    |--------------------------------------------------------------------------
    */

    public function storeSection(Request $request, Course $course)
    {
        $this->authorizeCourse($course);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ], [
            'title.required' => 'Введите название секции.',
            'title.max' => 'Название секции не должно превышать 255 символов.',
        ]);

        $course->sections()->create([
            'title' => $data['title'],
            'sort_order' => (int) $course->sections()->max('sort_order') + 1,
        ]);

        return back()->with('success', 'Секция добавлена.');
    }

    public function updateSection(Request $request, Section $section)
    {
        $this->authorizeCourse($section->course);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ], [
            'title.required' => 'Введите название секции.',
        ]);

        $section->update($data);

        return back()->with('success', 'Секция переименована.');
    }

    public function destroySection(Section $section)
    {
        $this->authorizeCourse($section->course);

        $section->delete(); // уроки удалятся каскадно по FK

        return back()->with('success', 'Секция удалена.');
    }

    public function moveSection(Section $section, string $direction)
    {
        $this->authorizeCourse($section->course);

        $siblings = Section::where('course_id', $section->course_id)
            ->orderBy('sort_order')->orderBy('id')->get();

        $this->swap($siblings, $section->id, $direction);

        return back();
    }

    /*
    |--------------------------------------------------------------------------
    | Уроки
    |--------------------------------------------------------------------------
    */

    public function storeLesson(Request $request, Section $section)
    {
        $this->authorizeCourse($section->course);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:video,text,test,task'],
        ], [
            'title.required' => 'Введите название урока.',
            'type.in' => 'Недопустимый тип урока.',
        ]);

        $section->lessons()->create([
            'title' => $data['title'],
            'type' => $data['type'],
            'sort_order' => (int) $section->lessons()->max('sort_order') + 1,
        ]);

        // Пересчитываем длительность курса
        $section->course->recalculateDuration();

        return back()->with('success', 'Урок добавлен.');
    }

    public function updateLesson(Request $request, Lesson $lesson)
    {
        $this->authorizeCourse($lesson->section->course);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:video,text,test,task'],
            'video_url' => ['nullable', 'string', 'max:500'],  // ← ослаблено: просто строка
            'content' => ['nullable', 'string'],
            'duration_minutes' => ['nullable', 'integer', 'min:0', 'max:600'],
            'video_file' => ['nullable', 'file', 'mimes:mp4,webm,mov', 'max:512000'],
        ], [
            'title.required' => 'Введите название урока.',
            'type.in' => 'Недопустимый тип урока.',
            'video_url.max' => 'Ссылка на видео слишком длинная.',
            'duration_minutes.integer' => 'Длительность указывается в целых минутах.',
            'video_file.mimes' => 'Допустимые форматы видео: mp4, webm, mov.',
            'video_file.max' => 'Размер видеофайла не должен превышать 500 МБ.',
        ]);

        $data['is_free_preview'] = $request->boolean('is_free_preview');

        // Нормализация URL: добавляем https:// если нет протокола
        if (!empty($data['video_url']) && !preg_match('/^https?:\/\//', $data['video_url'])) {
            $data['video_url'] = 'https://' . $data['video_url'];
        }

        // Ссылка на видео имеет смысл только для видео-уроков
        if ($data['type'] !== Lesson::TYPE_VIDEO) {
            $data['video_url'] = null;
            $data['video_path'] = null; // также очищаем файл, если сменили тип
        }

        $lesson->update($data);

        // Загрузка видеофайла (имеет приоритет над ссылкой)
        if ($request->hasFile('video_file')) {
            if ($lesson->video_path) {
                Storage::disk('public')->delete($lesson->video_path);
            }

            $lesson->update(['video_path' => $request->file('video_file')->store('videos', 'public')]);
        }

        $lesson->section->course->recalculateDuration();

        return back()->with('success', 'Урок сохранён.');
    }

    public function destroyLesson(Lesson $lesson)
    {
        $course = $lesson->section->course;

        $this->authorizeCourse($course);

        foreach ($lesson->materials as $material) {
            Storage::disk('public')->delete($material->path);
        }

        $lesson->delete();

        // Пересчитываем длительность курса
        $course->recalculateDuration();

        return back()->with('success', 'Урок удалён.');
    }

    public function moveLesson(Lesson $lesson, string $direction)
    {
        $this->authorizeCourse($lesson->section->course);

        $siblings = Lesson::where('section_id', $lesson->section_id)
            ->orderBy('sort_order')->orderBy('id')->get();

        $this->swap($siblings, $lesson->id, $direction);

        return back();
    }

    /*
    |--------------------------------------------------------------------------
    | Материалы
    |--------------------------------------------------------------------------
    */

    public function storeMaterial(Request $request, Lesson $lesson)
    {
        $this->authorizeCourse($lesson->section->course);

        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'file' => ['required', 'file', 'max:51200'],
        ], [
            'file.required' => 'Выберите файл для загрузки.',
            'file.file' => 'Не удалось загрузить файл.',
            'file.max' => 'Размер файла не должен превышать 50 МБ.',
        ]);

        $file = $request->file('file');

        $lesson->materials()->create([
            'title' => $data['title'] ?: $file->getClientOriginalName(),
            'path' => $file->store('materials', 'public'),
            'size_bytes' => $file->getSize(),
        ]);

        return back()->with('success', 'Материал загружен.');
    }

    public function destroyMaterial(LessonMaterial $material)
    {
        $this->authorizeCourse($material->lesson->section->course);

        Storage::disk('public')->delete($material->path);
        $material->delete();

        return back()->with('success', 'Материал удалён.');
    }

    /*
    |--------------------------------------------------------------------------
    | Хелперы
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
     * Меняет местами элемент с соседним в упорядоченной коллекции
     * и пересохраняет порядковые номера.
     */
    private function swap(Collection $siblings, int $id, string $direction): void
    {
        $ids = $siblings->pluck('id')->values();
        $index = $ids->search($id);

        if ($index === false) {
            return;
        }

        $target = $direction === 'up' ? $index - 1 : $index + 1;

        if ($target < 0 || $target >= $ids->count()) {
            return;
        }

        $ids->put($index, $ids[$target]);
        $ids->put($target, $id);

        foreach ($ids as $position => $modelId) {
            $siblings->firstWhere('id', $modelId)->update(['sort_order' => $position + 1]);
        }
    }
}