<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Tag;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::query()
            ->where('status', Course::STATUS_PUBLISHED)
            ->with(['author', 'tags']);

        // Умный поиск: название, описание, теги
        if ($q = trim((string) $request->query('q'))) {
            $query->where(function ($qq) use ($q) {
                $qq->where('title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhereHas('tags', fn ($t) => $t->where('name', 'like', "%{$q}%"));
            });
        }

        if ($access = $request->query('access')) {
            $query->whereIn('access_type', (array) $access);
        }

        if ($level = $request->query('level')) {
            $query->whereIn('level', (array) $level);
        }

        if ($format = $request->query('format')) {
            $query->where('format', $format);
        }

        if ($rating = $request->query('rating')) {
            $query->where('rating_avg', '>=', (float) $rating);
        }

        match ($request->query('duration')) {
            'short' => $query->where('duration_minutes', '<', 300),
            'medium' => $query->whereBetween('duration_minutes', [300, 600]),
            'long' => $query->where('duration_minutes', '>', 600),
            default => null,
        };

        if ($tag = $request->query('tag')) {
            $query->whereHas('tags', fn ($t) => $t->where('slug', $tag));
        }

        match ($request->query('sort', 'newest')) {
            'rating' => $query->orderByDesc('rating_avg'),
            'popular' => $query->orderByDesc('rating_count'),
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            default => $query->orderByDesc('created_at'),
        };

        $courses = $query->paginate(9)->withQueryString();
        $tags = Tag::orderBy('name')->limit(20)->get();

        return view('catalog.index', compact('courses', 'tags'));
    }
}