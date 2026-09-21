<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title.' — '.($dealership->name ?? config('app.name')) : ($dealership->name ?? config('app.name')) }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-base-200 text-base-content antialiased">
    @php
        $dealership = $dealership ?? \App\Models\Dealership::current();
        $externalLinks = $externalLinks ?? \App\Models\ExternalLink::query()->visible()->get();
    @endphp
    <x-nav sticky class="border-base-content/10">
        <x-slot:brand>
            <a href="{{ route('home') }}" wire:navigate class="flex flex-col">
                <span class="text-lg font-semibold tracking-tight">{{ $dealership->name ?? config('app.name') }}</span>
                @if($dealership?->localized('tagline'))
                    <span class="hidden text-xs text-base-content/60 sm:block">{{ $dealership->localized('tagline') }}</span>
                @endif
            </a>
        </x-slot:brand>
        <x-slot:actions>
            <div class="hidden items-center gap-4 text-sm md:flex">
                <a href="{{ route('home') }}" wire:navigate class="hover:text-primary">{{ __('nav.home') }}</a>
                <a href="{{ route('inventory') }}" wire:navigate class="hover:text-primary">{{ __('nav.inventory') }}</a>
                <a href="{{ route('contact') }}" wire:navigate class="hover:text-primary">{{ __('nav.contact') }}</a>
            </div>
            <livewire:language-switcher />
            <x-theme-toggle class="btn btn-ghost btn-sm btn-circle" />
            <div class="dropdown dropdown-end md:hidden">
                <div tabindex="0" role="button" class="btn btn-ghost btn-sm btn-circle">
                    <x-icon name="o-bars-3" class="h-5 w-5" />
                </div>
                <ul tabindex="-1" class="menu dropdown-content menu-sm z-20 mt-3 w-44 rounded-box border border-base-content/10 bg-base-100 p-2 shadow">
                    <li><a href="{{ route('home') }}" wire:navigate>{{ __('nav.home') }}</a></li>
                    <li><a href="{{ route('inventory') }}" wire:navigate>{{ __('nav.inventory') }}</a></li>
                    <li><a href="{{ route('contact') }}" wire:navigate>{{ __('nav.contact') }}</a></li>
                </ul>
            </div>
        </x-slot:actions>
    </x-nav>

    <main class="mx-auto w-full max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
        {{ $slot }}
    </main>

    <footer class="border-t border-base-content/10 bg-base-100">
        <div class="mx-auto flex max-w-6xl flex-col gap-4 px-4 py-8 text-sm text-base-content/70 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
            <p>{{ $dealership->name ?? config('app.name') }}</p>
            <x-social-links :links="$externalLinks ?? collect()" />
            <div class="flex flex-wrap gap-x-4 gap-y-1">
                <a href="{{ route('legal.privacy') }}" wire:navigate class="hover:text-primary">{{ __('legal.privacy') }}</a>
                <a href="{{ route('legal.cookies') }}" wire:navigate class="hover:text-primary">{{ __('legal.cookies') }}</a>
                <a href="{{ route('legal.terms') }}" wire:navigate class="hover:text-primary">{{ __('legal.terms') }}</a>
            </div>
            <p>{{ $dealership?->city }} @if($dealership?->phone) · {{ $dealership->phone }} @endif</p>
        </div>
    </footer>

    <livewire:cookie-banner />
    <x-toast />
</body>
</html>
