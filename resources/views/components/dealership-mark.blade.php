@props([
    'dealership' => null,
    'size' => 'md',
])

@php
    $dealership = $dealership ?? \App\Models\Dealership::current();
    $classes = match ($size) {
        'sm' => 'h-8 w-8',
        'lg' => 'h-12 w-12 sm:h-14 sm:w-14',
        default => 'h-9 w-9',
    };
@endphp

@if ($dealership?->logoUrl())
    <img src="{{ $dealership->logoUrl() }}" alt="{{ $dealership->name }}" {{ $attributes->class([$classes, 'shrink-0 rounded-lg object-contain']) }}>
@endif
