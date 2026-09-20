<?php

use App\Models\Brand;
use App\Models\Vehicle;
use App\Models\VehicleImage;
use App\Models\VehicleModel;
use App\Support\ImageResizer;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

new #[Layout('layouts::admin')] #[Title('Vehicle')] class extends Component
{
    use Toast, WithFileUploads;

    public ?int $vehicleId = null;

    public string $brand_id = '';

    public string $vehicle_model_id = '';

    public string $new_brand = '';

    public string $new_model = '';

    public string $year = '';

    public string $price = '';

    public string $mileage = '';

    public string $engine_type = 'gasoline';

    public string $transmission = 'automatic';

    public string $seats = '5';

    public string $color = '';

    public string $body_type = 'sedan';

    public string $drivetrain = 'fwd';

    public string $doors = '4';

    public string $description = '';

    public string $description_es = '';

    public string $status = 'available';

    public bool $featured = false;

    public string $vin = '';

    /** @var array<int, TemporaryUploadedFile> */
    public array $photos = [];

    public function mount(?Vehicle $vehicle = null): void
    {
        if (! $vehicle?->exists) {
            $this->year = (string) now()->year;

            return;
        }

        $this->vehicleId = $vehicle->id;
        $this->brand_id = (string) $vehicle->brand_id;
        $this->vehicle_model_id = (string) $vehicle->vehicle_model_id;
        $this->year = (string) $vehicle->year;
        $this->price = (string) $vehicle->price;
        $this->mileage = $vehicle->mileage !== null ? (string) $vehicle->mileage : '';
        $this->engine_type = $vehicle->engine_type;
        $this->transmission = $vehicle->transmission;
        $this->seats = (string) $vehicle->seats;
        $this->color = (string) $vehicle->color;
        $this->body_type = $vehicle->body_type;
        $this->drivetrain = (string) $vehicle->drivetrain;
        $this->doors = $vehicle->doors !== null ? (string) $vehicle->doors : '';
        $this->description = (string) $vehicle->description;
        $this->description_es = (string) $vehicle->description_es;
        $this->status = $vehicle->status;
        $this->featured = $vehicle->featured;
        $this->vin = (string) $vehicle->vin;
    }

    public function updatedBrandId(): void
    {
        $this->vehicle_model_id = '';
    }

    public function removePhoto(int $id): void
    {
        $image = VehicleImage::query()
            ->where('vehicle_id', $this->vehicleId)
            ->findOrFail($id);

        Storage::disk('public')->delete($image->path);
        $image->delete();
        $this->success(__('admin.photo_removed'));
    }

    public function save(): mixed
    {
        $this->validate([
            'brand_id' => ['nullable', 'exists:brands,id'],
            'vehicle_model_id' => ['nullable', 'exists:vehicle_models,id'],
            'new_brand' => ['nullable', 'string', 'max:80'],
            'new_model' => ['nullable', 'string', 'max:80'],
            'year' => ['required', 'integer', 'min:1980', 'max:2035'],
            'price' => ['required', 'numeric', 'min:0'],
            'mileage' => ['nullable', 'integer', 'min:0'],
            'engine_type' => ['required', Rule::in(['gasoline', 'diesel', 'hybrid', 'electric'])],
            'transmission' => ['required', Rule::in(['automatic', 'manual'])],
            'seats' => ['required', 'integer', 'min:1', 'max:15'],
            'color' => ['nullable', 'string', 'max:40'],
            'body_type' => ['required', Rule::in(['sedan', 'suv', 'hatchback', 'truck', 'coupe', 'van'])],
            'drivetrain' => ['nullable', Rule::in(['fwd', 'rwd', 'awd'])],
            'doors' => ['nullable', 'integer', 'min:2', 'max:6'],
            'description' => ['nullable', 'string', 'max:5000'],
            'description_es' => ['nullable', 'string', 'max:5000'],
            'status' => ['required', Rule::in(['available', 'reserved', 'sold'])],
            'featured' => ['boolean'],
            'vin' => ['nullable', 'string', 'max:32'],
            'photos' => ['array', 'max:8'],
            'photos.*' => ['image', 'max:2048', 'mimes:jpg,jpeg,png,webp'],
        ]);

        $brand = $this->resolveBrand();
        $model = $this->resolveModel($brand);

        $payload = [
            'brand_id' => $brand->id,
            'vehicle_model_id' => $model->id,
            'year' => (int) $this->year,
            'price' => $this->price,
            'mileage' => $this->mileage !== '' ? (int) $this->mileage : null,
            'engine_type' => $this->engine_type,
            'transmission' => $this->transmission,
            'seats' => (int) $this->seats,
            'color' => $this->color ?: null,
            'body_type' => $this->body_type,
            'drivetrain' => $this->drivetrain ?: null,
            'doors' => $this->doors !== '' ? (int) $this->doors : null,
            'description' => $this->description ?: null,
            'description_es' => $this->description_es ?: null,
            'status' => $this->status,
            'featured' => $this->featured,
            'vin' => $this->vin ?: null,
        ];

        $vehicle = $this->vehicleId
            ? Vehicle::query()->with('images')->findOrFail($this->vehicleId)
            : new Vehicle;

        $payload['slug'] = $this->uniqueSlug($brand->name, $model->name, (int) $this->year, $vehicle->id);

        $vehicle->fill($payload)->save();
        $this->vehicleId = $vehicle->id;

        $this->storePhotos($vehicle);
        $this->photos = [];

        $this->success(__('admin.vehicle_saved'));

        return $this->redirectRoute('admin.vehicles.edit', $vehicle, navigate: true);
    }

    public function with(): array
    {
        $brands = Brand::query()->orderBy('name')->get();
        $models = VehicleModel::query()
            ->when($this->brand_id, fn ($query) => $query->where('brand_id', $this->brand_id))
            ->orderBy('name')
            ->get();

        $vehicle = $this->vehicleId
            ? Vehicle::query()->with('images')->find($this->vehicleId)
            : null;

        return [
            'brands' => $brands,
            'models' => $models,
            'vehicle' => $vehicle,
            'engines' => ['gasoline', 'diesel', 'hybrid', 'electric'],
            'transmissions' => ['automatic', 'manual'],
            'bodies' => ['sedan', 'suv', 'hatchback', 'truck', 'coupe', 'van'],
            'drives' => ['fwd', 'rwd', 'awd'],
            'statuses' => ['available', 'reserved', 'sold'],
        ];
    }

    private function resolveBrand(): Brand
    {
        if (filled($this->new_brand)) {
            $name = trim($this->new_brand);

            return Brand::query()->firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name],
            );
        }

        if ($this->brand_id === '') {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'brand_id' => __('admin.brand_required'),
            ]);
        }

        return Brand::query()->findOrFail($this->brand_id);
    }

    private function resolveModel(Brand $brand): VehicleModel
    {
        if (filled($this->new_model)) {
            $name = trim($this->new_model);

            return VehicleModel::query()->firstOrCreate(
                ['brand_id' => $brand->id, 'slug' => Str::slug($name)],
                ['name' => $name],
            );
        }

        if ($this->vehicle_model_id === '') {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'vehicle_model_id' => __('admin.model_required'),
            ]);
        }

        return VehicleModel::query()->where('brand_id', $brand->id)->findOrFail($this->vehicle_model_id);
    }

    private function uniqueSlug(string $brand, string $model, int $year, ?int $ignoreId): string
    {
        $base = Str::slug($year.' '.$brand.' '.$model) ?: 'vehicle';
        $slug = $base;
        $i = 1;

        while (Vehicle::query()->where('slug', $slug)->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }

    private function storePhotos(Vehicle $vehicle): void
    {
        $existing = $vehicle->images()->count();
        $order = $existing;

        foreach ($this->photos as $photo) {
            if ($existing >= 8) {
                break;
            }

            $path = ImageResizer::store($photo, 'vehicles/'.$vehicle->id);
            $vehicle->images()->create([
                'path' => $path,
                'sort_order' => $order++,
            ]);
            $existing++;
        }
    }
};
?>

<div class="space-y-6">
    <div class="flex items-center justify-between gap-3">
        <h1 class="text-2xl font-semibold">{{ $vehicleId ? __('admin.edit_vehicle') : __('admin.add_vehicle') }}</h1>
        <a href="{{ route('admin.vehicles') }}" wire:navigate class="btn btn-ghost btn-sm">{{ __('admin.back') }}</a>
    </div>

    <form wire:submit="save" class="space-y-6">
        <section class="rounded-2xl border border-base-content/10 bg-base-100 p-5">
            <h2 class="mb-4 text-lg font-semibold">{{ __('admin.identity') }}</h2>
            <div class="grid gap-4 sm:grid-cols-2">
                <label class="form-control">
                    <span class="mb-1 text-sm">{{ __('search.brand') }}</span>
                    <select wire:model.live="brand_id" class="select select-bordered w-full">
                        <option value="">{{ __('search.any') }}</option>
                        @foreach ($brands as $brand)
                            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                        @endforeach
                    </select>
                    @error('brand_id') <span class="mt-1 text-sm text-error">{{ $message }}</span> @enderror
                </label>
                <label class="form-control">
                    <span class="mb-1 text-sm">{{ __('admin.new_brand') }}</span>
                    <input type="text" wire:model="new_brand" class="input input-bordered w-full" placeholder="{{ __('admin.new_brand_hint') }}">
                </label>
                <label class="form-control">
                    <span class="mb-1 text-sm">{{ __('search.model') }}</span>
                    <select wire:model="vehicle_model_id" class="select select-bordered w-full">
                        <option value="">{{ __('search.any') }}</option>
                        @foreach ($models as $model)
                            <option value="{{ $model->id }}">{{ $model->name }}</option>
                        @endforeach
                    </select>
                    @error('vehicle_model_id') <span class="mt-1 text-sm text-error">{{ $message }}</span> @enderror
                </label>
                <label class="form-control">
                    <span class="mb-1 text-sm">{{ __('admin.new_model') }}</span>
                    <input type="text" wire:model="new_model" class="input input-bordered w-full" placeholder="{{ __('admin.new_model_hint') }}">
                </label>
            </div>
        </section>

        <section class="rounded-2xl border border-base-content/10 bg-base-100 p-5">
            <h2 class="mb-4 text-lg font-semibold">{{ __('vehicle.specs') }}</h2>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <label class="form-control">
                    <span class="mb-1 text-sm">{{ __('vehicle.year') }}</span>
                    <input type="number" wire:model="year" class="input input-bordered w-full" min="1980" max="2035" required>
                    @error('year') <span class="mt-1 text-sm text-error">{{ $message }}</span> @enderror
                </label>
                <label class="form-control">
                    <span class="mb-1 text-sm">{{ __('vehicle.price') }}</span>
                    <input type="number" wire:model="price" class="input input-bordered w-full" min="0" step="50" required>
                    @error('price') <span class="mt-1 text-sm text-error">{{ $message }}</span> @enderror
                </label>
                <label class="form-control">
                    <span class="mb-1 text-sm">{{ __('vehicle.mileage') }}</span>
                    <input type="number" wire:model="mileage" class="input input-bordered w-full" min="0">
                </label>
                <label class="form-control">
                    <span class="mb-1 text-sm">{{ __('vehicle.seats') }}</span>
                    <input type="number" wire:model="seats" class="input input-bordered w-full" min="1" max="15">
                </label>
                <label class="form-control">
                    <span class="mb-1 text-sm">{{ __('vehicle.engine') }}</span>
                    <select wire:model="engine_type" class="select select-bordered w-full">
                        @foreach ($engines as $engine)
                            <option value="{{ $engine }}">{{ __('engine.'.$engine) }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="form-control">
                    <span class="mb-1 text-sm">{{ __('vehicle.transmission') }}</span>
                    <select wire:model="transmission" class="select select-bordered w-full">
                        @foreach ($transmissions as $item)
                            <option value="{{ $item }}">{{ __('transmission.'.$item) }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="form-control">
                    <span class="mb-1 text-sm">{{ __('search.body') }}</span>
                    <select wire:model="body_type" class="select select-bordered w-full">
                        @foreach ($bodies as $body)
                            <option value="{{ $body }}">{{ __('body.'.$body) }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="form-control">
                    <span class="mb-1 text-sm">{{ __('vehicle.drivetrain') }}</span>
                    <select wire:model="drivetrain" class="select select-bordered w-full">
                        <option value="">—</option>
                        @foreach ($drives as $drive)
                            <option value="{{ $drive }}">{{ __('drivetrain.'.$drive) }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="form-control">
                    <span class="mb-1 text-sm">{{ __('vehicle.color') }}</span>
                    <input type="text" wire:model="color" class="input input-bordered w-full">
                </label>
                <label class="form-control">
                    <span class="mb-1 text-sm">{{ __('vehicle.doors') }}</span>
                    <input type="number" wire:model="doors" class="input input-bordered w-full" min="2" max="6">
                </label>
                <label class="form-control">
                    <span class="mb-1 text-sm">{{ __('admin.status') }}</span>
                    <select wire:model="status" class="select select-bordered w-full">
                        @foreach ($statuses as $item)
                            <option value="{{ $item }}">{{ __('listing.'.$item) }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="form-control">
                    <span class="mb-1 text-sm">VIN</span>
                    <input type="text" wire:model="vin" class="input input-bordered w-full">
                </label>
            </div>
            <label class="mt-4 flex items-center gap-2">
                <input type="checkbox" wire:model="featured" class="checkbox checkbox-sm">
                <span>{{ __('admin.featured') }}</span>
            </label>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <label class="form-control">
                    <span class="mb-1 text-sm">{{ __('admin.description_en') }}</span>
                    <textarea wire:model="description" class="textarea textarea-bordered w-full" rows="4"></textarea>
                </label>
                <label class="form-control">
                    <span class="mb-1 text-sm">{{ __('admin.description_es') }}</span>
                    <textarea wire:model="description_es" class="textarea textarea-bordered w-full" rows="4"></textarea>
                </label>
            </div>
        </section>

        <section class="rounded-2xl border border-base-content/10 bg-base-100 p-5">
            <h2 class="mb-2 text-lg font-semibold">{{ __('admin.photos') }}</h2>
            <p class="mb-4 text-sm text-base-content/60">{{ __('admin.photos_hint') }}</p>

            @if ($vehicle?->images?->isNotEmpty())
                <div class="mb-4 grid grid-cols-2 gap-3 sm:grid-cols-4">
                    @foreach ($vehicle->images as $image)
                        <figure class="relative overflow-hidden rounded-xl bg-base-200">
                            <img src="{{ $image->url() }}" alt="" class="h-28 w-full object-cover">
                            <button type="button" class="btn btn-xs btn-error absolute right-2 top-2" wire:click="removePhoto({{ $image->id }})">
                                {{ __('admin.delete') }}
                            </button>
                        </figure>
                    @endforeach
                </div>
            @endif

            <input type="file" wire:model="photos" class="file-input file-input-bordered w-full max-w-md" accept="image/jpeg,image/png,image/webp" multiple>
            <div wire:loading wire:target="photos" class="mt-2 text-sm text-base-content/60">{{ __('admin.uploading') }}</div>
            @error('photos.*') <p class="mt-2 text-sm text-error">{{ $message }}</p> @enderror
        </section>

        <button type="submit" class="btn btn-primary">{{ __('admin.save') }}</button>
    </form>
</div>
