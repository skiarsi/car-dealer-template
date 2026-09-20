<?php

use App\Models\Brand;
use App\Models\Vehicle;
use App\Models\VehicleModel;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Inventory')] class extends Component
{
    use WithPagination;

    #[Url]
    public string $q = '';

    #[Url]
    public string $brand_id = '';

    #[Url]
    public string $vehicle_model_id = '';

    #[Url]
    public string $year_from = '';

    #[Url]
    public string $year_to = '';

    #[Url]
    public string $price_min = '';

    #[Url]
    public string $price_max = '';

    #[Url]
    public string $seats = '';

    #[Url]
    public string $engine_type = '';

    #[Url]
    public string $transmission = '';

    #[Url]
    public string $body_type = '';

    public function mount(): void
    {
        foreach (['q', 'brand_id', 'vehicle_model_id', 'year_from', 'year_to', 'price_min', 'price_max', 'seats', 'engine_type', 'transmission', 'body_type'] as $field) {
            $value = request()->query($field);
            if (is_string($value) && $value !== '') {
                $this->{$field} = $value;
            }
        }
    }

    public function updatedBrandId(): void
    {
        $this->vehicle_model_id = '';
        $this->resetPage();
    }

    public function updated($property): void
    {
        if ($property !== 'vehicle_model_id' || $this->getErrorBag()->isEmpty()) {
            $this->resetPage();
        }
    }

    public function resetFilters(): void
    {
        $this->reset([
            'q', 'brand_id', 'vehicle_model_id', 'year_from', 'year_to',
            'price_min', 'price_max', 'seats', 'engine_type', 'transmission', 'body_type',
        ]);
        $this->resetPage();
    }

    public function with(): array
    {
        $filters = [
            'q' => $this->q,
            'brand_id' => $this->brand_id,
            'vehicle_model_id' => $this->vehicle_model_id,
            'year_from' => $this->year_from,
            'year_to' => $this->year_to,
            'price_min' => $this->price_min,
            'price_max' => $this->price_max,
            'seats' => $this->seats,
            'engine_type' => $this->engine_type,
            'transmission' => $this->transmission,
            'body_type' => $this->body_type,
        ];

        $vehicles = Vehicle::query()
            ->available()
            ->with(['brand', 'vehicleModel', 'images'])
            ->search($filters)
            ->orderByDesc('year')
            ->orderBy('price')
            ->paginate(9);

        $models = $this->brand_id
            ? VehicleModel::query()->where('brand_id', $this->brand_id)->orderBy('name')->get()
            : VehicleModel::query()->orderBy('name')->get();

        return [
            'brands' => Brand::query()->orderBy('name')->get(),
            'models' => $models,
            'vehicles' => $vehicles,
            'engines' => ['gasoline', 'diesel', 'hybrid', 'electric'],
            'transmissions' => ['automatic', 'manual'],
            'bodies' => ['sedan', 'suv', 'hatchback', 'truck', 'coupe', 'van'],
        ];
    }
};
?>

<div class="space-y-8">
    <header class="max-w-2xl space-y-2">
        <h1 class="text-3xl font-semibold tracking-tight">{{ __('inventory.title') }}</h1>
        <p class="text-base-content/70">{{ __('inventory.lead') }}</p>
    </header>

    <form wire:submit.prevent class="rounded-2xl border border-base-content/10 bg-base-100 p-5">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <label class="form-control sm:col-span-2">
                <span class="mb-1 text-sm">{{ __('search.keyword') }}</span>
                <input type="text" wire:model.live.debounce.400ms="q" class="input input-bordered w-full" placeholder="{{ __('search.keyword_placeholder') }}">
            </label>
            <label class="form-control">
                <span class="mb-1 text-sm">{{ __('search.brand') }}</span>
                <select wire:model.live="brand_id" class="select select-bordered w-full">
                    <option value="">{{ __('search.any') }}</option>
                    @foreach ($brands as $brand)
                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                    @endforeach
                </select>
            </label>
            <label class="form-control">
                <span class="mb-1 text-sm">{{ __('search.model') }}</span>
                <select wire:model.live="vehicle_model_id" class="select select-bordered w-full">
                    <option value="">{{ __('search.any') }}</option>
                    @foreach ($models as $model)
                        <option value="{{ $model->id }}">{{ $model->name }}</option>
                    @endforeach
                </select>
            </label>
            <label class="form-control">
                <span class="mb-1 text-sm">{{ __('search.year_from') }}</span>
                <input type="number" wire:model.live.debounce.400ms="year_from" class="input input-bordered w-full" min="1990" max="2030">
            </label>
            <label class="form-control">
                <span class="mb-1 text-sm">{{ __('search.year_to') }}</span>
                <input type="number" wire:model.live.debounce.400ms="year_to" class="input input-bordered w-full" min="1990" max="2030">
            </label>
            <label class="form-control">
                <span class="mb-1 text-sm">{{ __('search.price_min') }}</span>
                <input type="number" wire:model.live.debounce.400ms="price_min" class="input input-bordered w-full" min="0" step="100">
            </label>
            <label class="form-control">
                <span class="mb-1 text-sm">{{ __('search.price_max') }}</span>
                <input type="number" wire:model.live.debounce.400ms="price_max" class="input input-bordered w-full" min="0" step="100">
            </label>
            <label class="form-control">
                <span class="mb-1 text-sm">{{ __('search.seats') }}</span>
                <select wire:model.live="seats" class="select select-bordered w-full">
                    <option value="">{{ __('search.any') }}</option>
                    @foreach ([2, 4, 5, 7] as $count)
                        <option value="{{ $count }}">{{ $count }}+</option>
                    @endforeach
                </select>
            </label>
            <label class="form-control">
                <span class="mb-1 text-sm">{{ __('search.engine') }}</span>
                <select wire:model.live="engine_type" class="select select-bordered w-full">
                    <option value="">{{ __('search.any') }}</option>
                    @foreach ($engines as $engine)
                        <option value="{{ $engine }}">{{ __('engine.'.$engine) }}</option>
                    @endforeach
                </select>
            </label>
            <label class="form-control">
                <span class="mb-1 text-sm">{{ __('search.transmission') }}</span>
                <select wire:model.live="transmission" class="select select-bordered w-full">
                    <option value="">{{ __('search.any') }}</option>
                    @foreach ($transmissions as $item)
                        <option value="{{ $item }}">{{ __('transmission.'.$item) }}</option>
                    @endforeach
                </select>
            </label>
            <label class="form-control">
                <span class="mb-1 text-sm">{{ __('search.body') }}</span>
                <select wire:model.live="body_type" class="select select-bordered w-full">
                    <option value="">{{ __('search.any') }}</option>
                    @foreach ($bodies as $body)
                        <option value="{{ $body }}">{{ __('body.'.$body) }}</option>
                    @endforeach
                </select>
            </label>
        </div>
        <div class="mt-4">
            <button type="button" class="btn btn-ghost btn-sm" wire:click="resetFilters">{{ __('search.reset') }}</button>
        </div>
    </form>

    <p class="text-sm text-base-content/60">{{ __('search.results', ['count' => $vehicles->total()]) }}</p>

    @if ($vehicles->isEmpty())
        <p class="rounded-2xl border border-dashed border-base-content/15 p-8 text-center text-base-content/60">
            {{ __('search.empty') }}
        </p>
    @else
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($vehicles as $vehicle)
                <x-vehicle-card :vehicle="$vehicle" />
            @endforeach
        </div>
        <div class="mt-6">
            {{ $vehicles->links() }}
        </div>
    @endif
</div>
