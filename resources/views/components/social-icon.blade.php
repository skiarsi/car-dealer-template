@props(['platform' => 'website'])

@php
    $platform = strtolower((string) $platform);
@endphp

<span {{ $attributes->class(['inline-flex h-5 w-5 items-center justify-center']) }}>
    @switch($platform)
        @case('instagram')
            <svg viewBox="0 0 24 24" class="h-5 w-5 fill-current" aria-hidden="true"><path d="M7 3h10a4 4 0 0 1 4 4v10a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4V7a4 4 0 0 1 4-4zm10 1.8H7A2.2 2.2 0 0 0 4.8 7v10A2.2 2.2 0 0 0 7 19.2h10a2.2 2.2 0 0 0 2.2-2.2V7A2.2 2.2 0 0 0 17 4.8zM12 8.2A3.8 3.8 0 1 1 8.2 12 3.8 3.8 0 0 1 12 8.2zm0 1.6A2.2 2.2 0 1 0 14.2 12 2.2 2.2 0 0 0 12 9.8zm4.55-3.05a.95.95 0 1 1-.95.95.95.95 0 0 1 .95-.95z"/></svg>
            @break
        @case('facebook')
            <svg viewBox="0 0 24 24" class="h-5 w-5 fill-current" aria-hidden="true"><path d="M14 9h3V6h-3c-2.2 0-4 1.8-4 4v2H8v3h2v7h3v-7h2.2l.8-3H13v-2c0-.6.4-1 1-1z"/></svg>
            @break
        @case('x')
            <svg viewBox="0 0 24 24" class="h-5 w-5 fill-current" aria-hidden="true"><path d="M4 4h4.2l4.1 5.7L16.8 4H20l-6.3 8.2L20 20h-4.2l-4.4-6.1L7.2 20H4l6.6-8.6z"/></svg>
            @break
        @case('youtube')
            <svg viewBox="0 0 24 24" class="h-5 w-5 fill-current" aria-hidden="true"><path d="M22 8.2a3 3 0 0 0-2.1-2.1C18.2 5.6 12 5.6 12 5.6s-6.2 0-7.9.5A3 3 0 0 0 2 8.2 32 32 0 0 0 2 12a32 32 0 0 0 .1 3.8 3 3 0 0 0 2.1 2.1c1.7.5 7.9.5 7.9.5s6.2 0 7.9-.5a3 3 0 0 0 2.1-2.1A32 32 0 0 0 22 12a32 32 0 0 0 0-3.8zM10 15.2V8.8L16 12z"/></svg>
            @break
        @case('tiktok')
            <svg viewBox="0 0 24 24" class="h-5 w-5 fill-current" aria-hidden="true"><path d="M14 3h2.2a5.4 5.4 0 0 0 4.3 4.1V9.5A7.5 7.5 0 0 1 16.4 8v7.1a5.4 5.4 0 1 1-5.4-5.4h.3v2.4h-.3a3 3 0 1 0 3 3V3z"/></svg>
            @break
        @case('whatsapp')
            <svg viewBox="0 0 24 24" class="h-5 w-5 fill-current" aria-hidden="true"><path d="M12 3.2A8.8 8.8 0 0 0 4.6 16.4L4 21l4.7-.6A8.8 8.8 0 1 0 12 3.2zm4.8 12.4c-.2.6-1.1 1-1.8 1.1-.5.1-1.1.2-3.5-.8-2.9-1.2-4.7-4.1-4.9-4.3s-1.5-2-1.5-3.8.9-2.7 1.3-3.1.8-.4 1.1-.4h.8c.3 0 .6 0 .9.7s1.1 2.7 1.2 2.9.2.4 0 .7c-.2.3-.3.5-.6.8s-.5.5-.2.9a6.7 6.7 0 0 0 2 2.5c.6.4 1 .5 1.3.3s1.1-1.3 1.4-1.7.6-.3.9-.2 2.4 1.1 2.6 1.3.2.4.1 1z"/></svg>
            @break
        @case('google')
            <svg viewBox="0 0 24 24" class="h-5 w-5 fill-current" aria-hidden="true"><path d="M12 4.8A7.2 7.2 0 0 0 6.5 7.7l2.1 1.6A4.3 4.3 0 0 1 12 7.6c1.2 0 2.2.4 3 .1h.1c.8.6 1.9 1.5 2.7 2.2A7.2 7.2 0 1 0 12 4.8zm7.2 3.6h-3v2.4h3V13h2.4v-2.2H22V8.4h-2.8z"/></svg>
            @break
        @default
            <svg viewBox="0 0 24 24" class="h-5 w-5 fill-current" aria-hidden="true"><path d="M14 5h5v5h-2V8.4l-6.3 6.3-1.4-1.4L15.6 7H14V5zM5 6h6v2H7v10h10v-4h2v6H5V6z"/></svg>
    @endswitch
</span>
