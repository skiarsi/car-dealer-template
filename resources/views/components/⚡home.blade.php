<?php

use App\Models\Brand;
use App\Models\Dealership;
use App\Models\Vehicle;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Home')] class extends Component
{
    public string $q = '';

    public string $brand_id = '';

    public string $price_max = '';

    public function search(): mixed
    {
        return $this->redirectRoute('inventory', array_filter([
            'q' => $this->q,
            'brand_id' => $this->brand_id,
            'price_max' => $this->price_max,
        ], fn ($value) => $value !== '' && $value !== null), navigate: true);
    }

    public function with(): array
    {
        return [
            'dealership' => Dealership::current(),
            'brands' => Brand::query()->orderBy('name')->get(),
            'featured' => Vehicle::query()
                ->available()
                ->featured()
                ->with(['brand', 'vehicleModel', 'images'])
                ->latest('year')
                ->take(3)
                ->get(),
        ];
    }
};
?>

<div class="space-y-12">
    <section class="max-w-2xl space-y-4">
        <p class="text-sm uppercase tracking-[0.2em] text-base-content/50">{{ __('home.kicker') }}</p>
        <h1 class="flex items-center gap-3 text-4xl font-semibold tracking-tight sm:text-5xl">
            <x-dealership-mark :dealership="$dealership" size="lg" />
            {{ $dealership->name ?? config('app.name') }}
        </h1>
        <p class="text-lg text-base-content/70">
            {{ $dealership?->localized('tagline') }}
        </p>
        <p class="max-w-xl text-base-content/70">
            {{ $dealership?->localized('about') }}
        </p>
    </section>

    <section class="rounded-2xl border border-base-content/10 bg-base-100 p-5 sm:p-6">
        <h2 class="text-lg font-semibold">{{ __('home.search_title') }}</h2>
        <form wire:submit="search" class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <label class="form-control">
                <span class="mb-1 text-sm">{{ __('search.keyword') }}</span>
                <input type="text" wire:model="q" class="input input-bordered w-full" placeholder="{{ __('search.keyword_placeholder') }}">
            </label>
            <label class="form-control">
                <span class="mb-1 text-sm">{{ __('search.brand') }}</span>
                <select wire:model="brand_id" class="select select-bordered w-full">
                    <option value="">{{ __('search.any') }}</option>
                    @foreach ($brands as $brand)
                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                    @endforeach
                </select>
            </label>
            <label class="form-control">
                <span class="mb-1 text-sm">{{ __('search.price_max') }}</span>
                <input type="number" min="0" step="100" wire:model="price_max" class="input input-bordered w-full">
            </label>
            <div class="flex items-end">
                <button type="submit" class="btn btn-primary w-full">{{ __('search.submit') }}</button>
            </div>
        </form>
    </section>

    <section class="space-y-5">
        <div class="flex items-end justify-between gap-4">
            <h2 class="text-xl font-semibold">{{ __('home.featured') }}</h2>
            <a href="{{ route('inventory') }}" wire:navigate class="text-sm text-primary">{{ __('home.browse_all') }}</a>
        </div>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($featured as $vehicle)
                <x-vehicle-card :vehicle="$vehicle" />
            @endforeach
        </div>
    </section>
</div>
