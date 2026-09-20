@props(['country'])

@php
    $country = strtolower((string) $country);
@endphp

<span {{ $attributes->class(['inline-flex overflow-hidden rounded-[2px] border border-black/10 leading-none']) }}>
    @if ($country === 'mx')
        <svg viewBox="0 0 9 6" class="h-4 w-6" aria-hidden="true">
            <rect width="3" height="6" fill="#006847"/>
            <rect x="3" width="3" height="6" fill="#fff"/>
            <rect x="6" width="3" height="6" fill="#ce1126"/>
            <circle cx="4.5" cy="3" r="0.85" fill="#8c6a32"/>
        </svg>
    @else
        <svg viewBox="0 0 19 10" class="h-4 w-6" aria-hidden="true">
            <rect width="19" height="10" fill="#bf0a30"/>
            <rect y="1.43" width="19" height="1.43" fill="#fff"/>
            <rect y="4.29" width="19" height="1.43" fill="#fff"/>
            <rect y="7.14" width="19" height="1.43" fill="#fff"/>
            <rect width="7.6" height="5.38" fill="#002868"/>
        </svg>
    @endif
</span>
