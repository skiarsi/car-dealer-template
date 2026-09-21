<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title.' — '.($dealership->name ?? config('app.name')) : ($dealership->name ?? config('app.name')) }}</title>
    @if($dealership?->logoUrl())
        <link rel="icon" href="{{ $dealership->logoUrl() }}" type="image/png">
        <link rel="apple-touch-icon" href="{{ $dealership->logoUrl() }}">
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-base-200 font-sans antialiased">
    @php($dealership = $dealership ?? \App\Models\Dealership::current())
    <x-nav sticky class="lg:hidden">
        <x-slot:brand>
            <span class="flex items-center gap-2 font-semibold">
                <x-dealership-mark :dealership="$dealership" size="sm" />
                {{ $dealership->name ?? config('app.name') }}
            </span>
        </x-slot:brand>
        <x-slot:actions>
            <livewire:language-switcher />
            <x-button icon="o-swatch" class="btn-ghost btn-sm btn-circle" @click="$dispatch('mary-toggle-theme')" />
            <label for="main-drawer" class="lg:hidden">
                <x-icon name="o-bars-3" class="cursor-pointer" />
            </label>
        </x-slot:actions>
    </x-nav>

    <x-main>
        <x-slot:sidebar drawer="main-drawer" collapsible class="bg-base-100 lg:bg-inherit">
            <a href="{{ route('dashboard') }}" wire:navigate class="hidden-when-collapsed flex items-center gap-2 px-5 pt-5 text-lg font-semibold">
                <x-dealership-mark :dealership="$dealership" size="sm" />
                {{ $dealership->name ?? config('app.name') }}
            </a>
            <div class="display-when-collapsed mt-5 hidden px-5">
                @if($dealership?->logoUrl())
                    <x-dealership-mark :dealership="$dealership" size="sm" />
                @else
                    <x-icon name="o-building-storefront" class="h-6 w-6" />
                @endif
            </div>

            <x-menu activate-by-route class="mt-4">
                <x-menu-item :title="__('admin.overview')" icon="o-home" :link="route('dashboard')" />
                <x-menu-item :title="__('admin.vehicles')" icon="o-truck" :link="route('admin.vehicles')" />
                <x-menu-item :title="__('admin.hours')" icon="o-clock" :link="route('admin.hours')" />
                <x-menu-item :title="__('admin.links')" icon="o-share" :link="route('admin.links')" />
                <livewire:admin.inquiry-nav />
                <x-menu-item :title="__('nav.inventory')" icon="o-globe-alt" :link="route('inventory')" />
                <x-menu-separator />
                @if($user = auth()->user())
                    <x-list-item :item="$user" value="name" sub-value="email" no-separator no-hover class="-mx-2 rounded">
                        <x-slot:actions>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-button icon="o-power" class="btn-circle btn-ghost btn-xs" type="submit" />
                            </form>
                        </x-slot:actions>
                    </x-list-item>
                @endif
                <div class="mt-4 hidden items-center gap-2 px-2 lg:flex">
                    <livewire:language-switcher />
                    <x-button icon="o-swatch" class="btn-ghost btn-sm btn-circle" @click="$dispatch('mary-toggle-theme')" />
                </div>
            </x-menu>
        </x-slot:sidebar>

        <x-slot:content>
            {{ $slot }}
        </x-slot:content>
    </x-main>

    <x-theme-toggle class="hidden" />
    <livewire:cookie-banner />
    <x-toast />
</body>
</html>
