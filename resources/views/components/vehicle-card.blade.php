@props(['vehicle'])

<article class="flex h-full flex-col overflow-hidden rounded-2xl border border-base-content/10 bg-base-100">
    @if ($vehicle->coverUrl())
        <a wire:navigate href="{{ route('vehicles.show', $vehicle) }}" class="block">
            <img src="{{ $vehicle->coverUrl() }}" alt="{{ $vehicle->title() }}" class="h-40 w-full object-cover">
        </a>
    @endif
    <div class="flex flex-1 flex-col p-5">
    <p class="text-sm text-base-content/60">{{ $vehicle->year }} · {{ __('body.'.$vehicle->body_type) }}</p>
    <h3 class="mt-1 text-lg font-semibold leading-snug">
        <a href="{{ route('vehicles.show', $vehicle) }}" wire:navigate class="hover:text-primary">
            {{ $vehicle->brand->name }} {{ $vehicle->vehicleModel->name }}
        </a>
    </h3>
    <p class="mt-3 text-2xl font-semibold tracking-tight">{{ $vehicle->formattedPrice() }}</p>
    <dl class="mt-4 grid grid-cols-2 gap-x-3 gap-y-2 text-sm text-base-content/80">
        <div>
            <dt class="text-xs uppercase tracking-wide text-base-content/50">{{ __('vehicle.engine') }}</dt>
            <dd>{{ __('engine.'.$vehicle->engine_type) }}</dd>
        </div>
        <div>
            <dt class="text-xs uppercase tracking-wide text-base-content/50">{{ __('vehicle.seats') }}</dt>
            <dd>{{ $vehicle->seats }}</dd>
        </div>
        <div>
            <dt class="text-xs uppercase tracking-wide text-base-content/50">{{ __('vehicle.mileage') }}</dt>
            <dd>{{ $vehicle->formattedMileage() ?? '—' }}</dd>
        </div>
        <div>
            <dt class="text-xs uppercase tracking-wide text-base-content/50">{{ __('vehicle.transmission') }}</dt>
            <dd>{{ __('transmission.'.$vehicle->transmission) }}</dd>
        </div>
    </dl>
    <div class="mt-5">
        <x-button :label="__('vehicle.view')" :link="route('vehicles.show', $vehicle)" class="btn-outline btn-sm" />
    </div>
    </div>
</article>
