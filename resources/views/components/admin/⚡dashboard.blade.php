<?php

use App\Models\Vehicle;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::admin')] #[Title('Dashboard')] class extends Component
{
    public function with(): array
    {
        return [
            'vehicleCount' => Vehicle::query()->available()->count(),
            'featuredCount' => Vehicle::query()->available()->featured()->count(),
        ];
    }
};
?>

<div class="space-y-8">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-2xl font-semibold">{{ __('admin.welcome') }}</h1>
        <a href="{{ route('admin.vehicles.create') }}" wire:navigate class="btn btn-primary btn-sm">{{ __('admin.add_vehicle') }}</a>
    </div>

    <div class="grid gap-4 sm:grid-cols-3">
        <x-stat :title="__('admin.vehicles')" :value="(string) $vehicleCount" icon="o-truck" />
        <livewire:admin.inquiry-count />
        <x-stat :title="__('admin.featured')" :value="(string) $featuredCount" icon="o-star" />
    </div>

    <section class="grid gap-3 sm:grid-cols-3">
        <a href="{{ route('admin.vehicles') }}" wire:navigate class="rounded-xl border border-base-content/10 bg-base-100 p-4 hover:border-primary">
            <p class="font-medium">{{ __('admin.vehicles') }}</p>
            <p class="text-sm text-base-content/60">{{ __('admin.manage_vehicles') }}</p>
        </a>
        <a href="{{ route('admin.hours') }}" wire:navigate class="rounded-xl border border-base-content/10 bg-base-100 p-4 hover:border-primary">
            <p class="font-medium">{{ __('admin.hours') }}</p>
            <p class="text-sm text-base-content/60">{{ __('admin.manage_hours') }}</p>
        </a>
        <a href="{{ route('admin.links') }}" wire:navigate class="rounded-xl border border-base-content/10 bg-base-100 p-4 hover:border-primary">
            <p class="font-medium">{{ __('admin.links') }}</p>
            <p class="text-sm text-base-content/60">{{ __('admin.manage_links') }}</p>
        </a>
    </section>

    <livewire:admin.latest-inquiries />
</div>
