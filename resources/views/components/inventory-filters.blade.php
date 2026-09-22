@props([
    'brands',
    'models',
    'engines',
    'transmissions',
    'bodies',
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
    <button type="button" class="btn btn-ghost btn-sm w-full" wire:click="resetFilters">{{ __('search.reset') }}</button>
</div>
