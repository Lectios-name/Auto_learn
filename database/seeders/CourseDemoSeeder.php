<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CourseDemoSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::where('email', 'author@test.com')->first();

        if (! $author) {
            $author = User::create([
                'name' => 'Demo Author',
                'email' => 'author@test.com',
                'password' => Hash::make('password'),
            ]);
            $author->assignRole('author');
        }

        $course = Course::create([
            'author_id' => $author->id,
            'title' => 'Laravel с нуля до профи',
            'slug' => 'laravel-s-nulya-do-profi',
            'description' => 'Демо-курс для проверки структуры и связей.',
            'access_type' => Course::ACCESS_PAID,
            'price' => 2990,
            'level' => Course::LEVEL_BEGINNER,
            'status' => Course::STATUS_PUBLISHED,
        ]);

        $intro = $course->sections()->create(['title' => 'Введение', 'sort_order' => 1]);
        $intro->lessons()->createMany([
            ['title' => 'Что такое Laravel', 'type' => 'video', 'video_url' => 'https://youtube.com/watch?v=demo1', 'duration_minutes' => 12, 'sort_order' => 1],
            ['title' => 'Установка окружения', 'type' => 'text', 'content' => 'Текст урока об установке...', 'duration_minutes' => 8, 'sort_order' => 2],
        ]);

        $mvc = $course->sections()->create(['title' => 'Основы MVC', 'sort_order' => 2]);
        $mvc->lessons()->create([
            'title' => 'Контроллеры и роутинг', 'type' => 'video', 'video_url' => 'https://youtube.com/watch?v=demo2', 'duration_minutes' => 15, 'sort_order' => 1,
        ]);
    }
}