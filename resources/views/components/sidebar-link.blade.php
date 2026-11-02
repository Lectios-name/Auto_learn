@props(['href' => '#', 'active' => false])

@php
    $base  = 'group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition';
    $state = $active
        ? 'bg-primary-50 text-primary-700'
        : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900';
    $iconState = $active ? 'text-primary-600' : 'text-gray-400 group-hover:text-gray-500';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => "$base $state"]) }}>
    @if (isset($icon))
        <span class="{{ $iconState }}">{{ $icon }}</span>
    @endif
    <span class="truncate">{{ $slot }}</span>
</a>