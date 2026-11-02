@props(['padding' => 'p-6'])

<div {{ $attributes->merge(['class' => "rounded-xl border border-gray-200 bg-white shadow-sm {$padding}"]) }}>
    @if (isset($header))
        <div class="mb-4 flex items-center justify-between gap-4">
            <h3 class="text-base font-semibold text-gray-900">{{ $header }}</h3>
            @if (isset($actions))<div>{{ $actions }}</div>@endif
        </div>
    @endif
    {{ $slot }}
</div>