<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class Lesson extends Model
{
    use HasFactory;

    public const TYPE_VIDEO = 'video';
    public const TYPE_TEXT = 'text';
    public const TYPE_TEST = 'test';
    public const TYPE_TASK = 'task';

    protected $fillable = [
        'section_id', 'title', 'type', 'content', 'video_url', 'video_path',
        'duration_minutes', 'is_free_preview', 'sort_order',
    ];

    protected function casts(): array
    {
        return ['is_free_preview' => 'boolean'];
    }

    /*
    |--------------------------------------------------------------------------
    | Связи
    |--------------------------------------------------------------------------
    */

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function materials(): HasMany
    {
        return $this->hasMany(LessonMaterial::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(LessonNote::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(LessonComment::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Видео: умное определение embed-адреса
    |--------------------------------------------------------------------------
    */

    /**
     * Ссылка указывает напрямую на видеофайл (.mp4 и т.п.)?
     */
    public function isDirectVideoFile(): bool
    {
        return preg_match('/\.(mp4|webm|ogg|mov)(\?.*)?$/i', trim((string) $this->video_url)) === 1;
    }

    /**
     * Возвращает адрес для iframe: сначала известные платформы,
     * затем универсальный oEmbed-детектор. null — встраивание невозможно.
     */
    public function embedUrl(): ?string
    {
        $url = trim((string) $this->video_url);

        if ($url === '' || $this->video_path || $this->isDirectVideoFile()) {
            return null;
        }

        foreach ($this->embedProviders() as $pattern => $builder) {
            if (preg_match($pattern, $url, $m)) {
                return $builder($m);
            }
        }

        return $this->discoverOEmbed($url);
    }

    /**
     * Известные видеоплатформы и правила превращения ссылки в embed.
     */
    private function embedProviders(): array
    {
        $twitchParent = parse_url(config('app.url'), PHP_URL_HOST) ?: 'localhost';

        return [
            // YouTube: watch, youtu.be, shorts, live, embed
            '/(?:youtube\.com\/(?:watch\?(?:.*&)?v=|embed\/|shorts\/|live\/)|youtu\.be\/)([\w-]{11})/'
                => fn ($m) => 'https://www.youtube.com/embed/'.$m[1],
            // Vimeo
            '/vimeo\.com\/(?:video\/)?(\d+)/'
                => fn ($m) => 'https://player.vimeo.com/video/'.$m[1],
            // Rutube
            '/rutube\.ru\/video\/(?:embed\/)?([a-f0-9]{32})/'
                => fn ($m) => 'https://rutube.ru/video/embed/'.$m[1],
            // VK Видео
            '/vk\.com\/(?:video|clip)(-?\d+_\d+)/'
                => fn ($m) => 'https://vk.com/video_ext.php?'.str_replace('_', '&id=', 'oid='.$m[1]),
            // Dailymotion
            '/dailymotion\.com\/video\/([a-zA-Z0-9]+)/'
                => fn ($m) => 'https://www.dailymotion.com/embed/video/'.$m[1],
            // Coub
            '/coub\.com\/(?:view\/)?([a-zA-Z0-9]+)/'
                => fn ($m) => 'https://coub.com/embed/'.$m[1],
            // OK.ru
            '/ok\.ru\/video\/(\d+)/'
                => fn ($m) => 'https://ok.ru/videoembed/'.$m[1],
            // Twitch (записи трансляций)
            '/twitch\.tv\/videos\/(\d+)/'
                => fn ($m) => 'https://player.twitch.tv/?video='.$m[1].'&parent='.$twitchParent,
        ];
    }

    /**
     * Универсальный oEmbed-детектор: спрашиваем у самого сайта,
     * как его встраивать. Результат кешируется на сутки.
     */
    private function discoverOEmbed(string $url): ?string
    {
        $cached = Cache::remember(
            'oembed_'.md5($url),
            now()->addDay(),
            fn () => $this->fetchOEmbed($url) ?? 'none'
        );

        return $cached === 'none' ? null : $cached;
    }

    private function fetchOEmbed(string $url): ?string
    {
        try {
            $page = Http::timeout(6)
                ->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; AutolearnBot/1.0)'])
                ->get($url)
                ->body();

            // Ищем <link rel="alternate" type="application/json+oembed" href="...">
            $found = preg_match(
                '/<link[^>]+type=["\']application\/json\+oembed["\'][^>]+href=["\']([^"\']+)["\']/',
                $page, $m
            ) || preg_match(
                '/<link[^>]+href=["\']([^"\']+)["\'][^>]+type=["\']application\/json\+oembed["\']/',
                $page, $m
            );

            if (! $found) {
                return null;
            }

            $data = Http::timeout(6)->get(html_entity_decode($m[1]))->json();

            // Из oEmbed-html достаём только src iframe (безопасность)
            if (! empty($data['html']) && preg_match('/<iframe[^>]+src=["\']([^"\']+)["\']/', $data['html'], $m2)) {
                return $m2[1];
            }

            return null;
        } catch (\Throwable $e) {
            return null; // сервис недоступен — покажем кнопку-фолбэк
        }
    }
}