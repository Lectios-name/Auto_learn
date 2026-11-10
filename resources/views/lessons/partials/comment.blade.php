@php $maxDepth = 2; @endphp

<div class="{{ $depth > 0 ? 'ml-6 border-l-2 border-gray-100 pl-4' : '' }}">
    <div class="flex items-start gap-3">
        <span class="flex h-8 w-8 flex-shrink-0 items-center justify-center overflow-hidden rounded-full bg-primary-100 text-xs font-bold text-primary-700">
            @if ($comment->user->avatar_path)
                <img src="{{ asset('storage/'.$comment->user->avatar_path) }}" alt="" class="h-8 w-8 object-cover">
            @else
                {{ strtoupper(mb_substr($comment->user->name, 0, 1)) }}
            @endif
        </span>

        <div class="flex-1">
            <div class="flex items-center gap-2">
                <span class="text-sm font-semibold text-gray-900">{{ $comment->user->name }}</span>
                <span class="text-xs text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
            </div>

            <p class="mt-1 text-sm text-gray-700">{{ $comment->content }}</p>

            @if ($depth < $maxDepth)
                <button @click="$refs.replyForm{{ $comment->id }}.classList.toggle('hidden')"
                        class="mt-2 text-xs font-medium text-primary-600 hover:text-primary-700">
                    Ответить
                </button>

                <form method="POST" action="{{ route('lessons.comments.store', $lesson) }}"
                      x-ref="replyForm{{ $comment->id }}" class="mt-3 hidden space-y-2">
                    @csrf
                    <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                    <textarea name="content" rows="2"
                              class="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500"
                              placeholder="Ваш ответ..."></textarea>
                    <x-btn type="submit" size="sm" variant="secondary">Ответить</x-btn>
                </form>
            @endif

            {{-- Рекурсивно рендерим ответы --}}
            @if ($comment->replies->isNotEmpty())
                <div class="mt-3 space-y-3">
                    @foreach ($comment->replies as $reply)
                        @include('lessons.partials.comment', ['comment' => $reply, 'depth' => $depth + 1])
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>