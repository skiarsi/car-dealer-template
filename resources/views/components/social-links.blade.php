@props(['links'])

@if ($links->isNotEmpty())
    <nav {{ $attributes->class(['flex flex-wrap items-center gap-3']) }} aria-label="{{ __('social.title') }}">
        @foreach ($links as $link)
            <a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 text-sm hover:text-primary">
                <x-social-icon :platform="$link->platform" />
                <span>{{ $link->label }}</span>
            </a>
        @endforeach
    </nav>
@endif
