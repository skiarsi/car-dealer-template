@props([
    'vehicle' => null,
    'featured' => null,
    'pinned' => null,
    'visible' => null,
])

@php
    $featured = $featured ?? (bool) $vehicle?->featured;
    $pinned = $pinned ?? (bool) $vehicle?->is_pinned;
    $visible = $visible ?? ($vehicle?->is_visible ?? true);
@endphp

@if ($featured || $pinned || ! $visible)
    <div {{ $attributes->class(['flex flex-wrap gap-1']) }}>
        @if ($featured)
            <span class="badge badge-warning badge-sm">{{ __('admin.featured_badge') }}</span>
        @endif
        @if ($pinned)
            <span class="badge badge-primary badge-sm">{{ __('admin.pinned_badge') }}</span>
        @endif
        @if (! $visible)
            <span class="badge badge-error badge-sm">{{ __('admin.hidden') }}</span>
        @endif
    </div>
@endif
