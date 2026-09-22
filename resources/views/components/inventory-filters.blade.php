@props([
    'brands',
    'models',
    'engines',
    'transmissions',
    'bodies',
    'yearMin',
    'yearMax',
    'priceMin',
    'priceMax',
    'yearFrom' => '',
    'yearTo' => '',
    'priceFrom' => '',
    'priceTo' => '',
])

<div class="space-y-4">
    <label class="form-control">
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
    <div class="form-control">
        <span class="mb-2 text-sm">{{ __('search.year') }}</span>
        <x-dual-range
            :min="$yearMin"
            :max="$yearMax"
            :from="$yearFrom"
            :to="$yearTo"
            method="setYearRange"
        />
    </div>
    <div class="form-control">
        <span class="mb-2 text-sm">{{ __('search.price') }}</span>
        <x-dual-range
            :min="$priceMin"
            :max="$priceMax"
            :step="500"
            :from="$priceFrom"
            :to="$priceTo"
            method="setPriceRange"
            format="price"
        />
    </div>
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
    <button type="button" class="btn btn-ghost btn-sm w-full" wire:click="resetFilters">{{ __('search.reset') }}</button>
</div>
