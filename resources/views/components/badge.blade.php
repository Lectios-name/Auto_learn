@props(['color' => 'gray'])

@php
    $colors = [
        'green'  => 'bg-primary-50 text-primary-700 ring-primary-600/20',
        'gray'   => 'bg-gray-50 text-gray-600 ring-gray-500/20',
        'yellow' => 'bg-yellow-50 text-yellow-700 ring-yellow-600/20',
        'red'    => 'bg-red-50 text-red-700 ring-red-600/20',
        'blue'   => 'bg-blue-50 text-blue-700 ring-blue-600/20',
    ][$color] ?? 'gray';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {$colors}"]) }}>
    {{ $slot }}
</span>