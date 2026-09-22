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

    public function setYearRange(mixed $from, mixed $to): void
    {
        [$this->year_from, $this->year_to] = $this->normalizeRange($from, $to);
        $this->resetPage();
    }

    public function setPriceRange(mixed $from, mixed $to): void
    {
        [$this->price_min, $this->price_max] = $this->normalizeRange($from, $to);
        $this->resetPage();
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function normalizeRange(mixed $from, mixed $to): array
    {
        $from = $from === '' || $from === null ? null : (int) $from;
        $to = $to === '' || $to === null ? null : (int) $to;

        if ($from !== null && $to !== null && $from > $to) {
            [$from, $to] = [$to, $from];
        }

        return [
            $from === null ? '' : (string) $from,
            $to === null ? '' : (string) $to,
        ];
    }

    /**
     * @return array{yearMin: int, yearMax: int, priceMin: int, priceMax: int}
     */
    private function catalogBounds(): array
    {
        $bounds = Vehicle::query()
            ->available()
            ->selectRaw('MIN(year) as min_year, MAX(year) as max_year, MIN(price) as min_price, MAX(price) as max_price')
            ->first();

        $yearMin = (int) ($bounds->min_year ?? now()->year - 15);
        $yearMax = (int) ($bounds->max_year ?? now()->year);
        $priceMin = (int) (floor(((float) ($bounds->min_price ?? 0)) / 500) * 500);
        $priceMax = (int) (ceil(((float) ($bounds->max_price ?? 100000)) / 500) * 500);

        if ($yearMax < $yearMin) {
            $yearMax = $yearMin;
        }

        if ($priceMax <= $priceMin) {
            $priceMax = $priceMin + 500;
        }

        return [
            'yearMin' => $yearMin,
            'yearMax' => $yearMax,
            'priceMin' => $priceMin,
            'priceMax' => $priceMax,
        ];
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
            ->pinnedFirst()
            ->paginate(9);

        $models = $this->brand_id
            ? VehicleModel::query()->where('brand_id', $this->brand_id)->orderBy('name')->get()
            : VehicleModel::query()->orderBy('name')->get();

        $bounds = $this->catalogBounds();

        return [
            'brands' => Brand::query()->orderBy('name')->get(),
            'models' => $models,
            'vehicles' => $vehicles,
            'engines' => ['gasoline', 'diesel', 'hybrid', 'electric'],
            'transmissions' => ['automatic', 'manual'],
            'bodies' => ['sedan', 'suv', 'hatchback', 'truck', 'coupe', 'van'],
            'activeFilterCount' => collect($filters)->filter(fn ($value) => $value !== '' && $value !== null)->count(),
            ...$bounds,
        ];
    }
};
?>

<div class="space-y-6">
    <header class="max-w-2xl space-y-2">
        <h1 class="text-3xl font-semibold tracking-tight">{{ __('inventory.title') }}</h1>
        <p class="text-base-content/70">{{ __('inventory.lead') }}</p>
    </header>

    <div class="relative lg:grid lg:grid-cols-[18rem_minmax(0,1fr)] lg:items-start lg:gap-8">
        <div class="lg:sticky lg:top-24 lg:max-h-[calc(100vh-8rem)]">
            <input id="inventory-filters-drawer" type="checkbox" class="peer sr-only" wire:ignore>

            <label
                for="inventory-filters-drawer"
                class="fixed inset-0 z-40 hidden bg-base-content/40 peer-checked:block lg:!hidden"
                aria-label="{{ __('search.close_filters') }}"
            ></label>

            <aside
                class="fixed inset-y-0 left-0 z-50 flex w-[min(20rem,90vw)] -translate-x-full flex-col overflow-y-auto border-r border-base-content/10 bg-base-100 p-5 shadow-xl transition-transform duration-200 peer-checked:translate-x-0 lg:static lg:z-auto lg:h-auto lg:max-h-[calc(100vh-8rem)] lg:w-full lg:translate-x-0 lg:rounded-2xl lg:border lg:p-5 lg:shadow-none"
            >
                <div class="mb-4 flex items-center justify-between gap-3 lg:block">
                    <h2 class="text-lg font-semibold">{{ __('search.filters') }}</h2>
                    <label for="inventory-filters-drawer" class="btn btn-ghost btn-sm btn-circle lg:hidden" aria-label="{{ __('search.close_filters') }}">
                        <x-icon name="o-x-mark" class="h-5 w-5" />
                    </label>
                </div>

                <x-inventory-filters
                    :brands="$brands"
                    :models="$models"
                    :engines="$engines"
                    :transmissions="$transmissions"
                    :bodies="$bodies"
                    :year-min="$yearMin"
                    :year-max="$yearMax"
                    :price-min="$priceMin"
                    :price-max="$priceMax"
                    :year-from="$year_from"
                    :year-to="$year_to"
                    :price-from="$price_min"
                    :price-to="$price_max"
                />

                <label for="inventory-filters-drawer" class="btn btn-primary mt-4 w-full lg:hidden">
                    {{ __('search.close_filters') }}
                </label>
            </aside>
        </div>

        <div class="min-w-0 space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <p class="text-sm text-base-content/60">{{ __('search.results', ['count' => $vehicles->total()]) }}</p>
                <label for="inventory-filters-drawer" class="btn btn-outline btn-sm lg:hidden">
                    <x-icon name="o-funnel" class="h-4 w-4" />
                    {{ __('search.filters') }}
                    @if ($activeFilterCount > 0)
                        <span class="badge badge-primary badge-sm">{{ $activeFilterCount }}</span>
                    @endif
                </label>
            </div>

            @if ($vehicles->isEmpty())
                <p class="rounded-2xl border border-dashed border-base-content/15 p-8 text-center text-base-content/60">
                    {{ __('search.empty') }}
                </p>
            @else
                <div class="grid gap-4 sm:grid-cols-2">
                    @foreach ($vehicles as $vehicle)
                        <x-vehicle-card :vehicle="$vehicle" />
                    @endforeach
                </div>
                <div class="mt-6">
                    {{ $vehicles->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
