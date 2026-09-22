<?php

namespace App\Models;

use App\Support\LocalizesAttributes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Vehicle extends Model
{
    /** @use HasFactory<\Database\Factories\VehicleFactory> */
    use HasFactory, LocalizesAttributes;

    protected $fillable = [
        'brand_id',
        'vehicle_model_id',
        'slug',
        'year',
        'price',
        'mileage',
        'engine_type',
        'transmission',
        'seats',
        'color',
        'body_type',
        'drivetrain',
        'doors',
        'description',
        'description_es',
        'status',
        'featured',
        'is_visible',
        'is_pinned',
        'vin',
    ];

    protected static function booted(): void
    {
        static::deleting(function (self $vehicle): void {
            $vehicle->images->each(function (VehicleImage $image): void {
                Storage::disk('public')->delete($image->path);
            });
        });
    }

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'price' => 'decimal:2',
            'mileage' => 'integer',
            'seats' => 'integer',
            'doors' => 'integer',
            'featured' => 'boolean',
            'is_visible' => 'boolean',
            'is_pinned' => 'boolean',
        ];
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function vehicleModel(): BelongsTo
    {
        return $this->belongsTo(VehicleModel::class);
    }

    public function inquiries(): HasMany
    {
        return $this->hasMany(Inquiry::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(VehicleImage::class)->orderBy('sort_order')->orderBy('id');
    }

    public function coverImage(): ?VehicleImage
    {
        if ($this->relationLoaded('images')) {
            return $this->images->first();
        }

        return $this->images()->first();
    }

    public function coverUrl(): ?string
    {
        return $this->coverImage()?->url();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function title(): string
    {
        return trim($this->year.' '.$this->brand?->name.' '.$this->vehicleModel?->name);
    }

    public function formattedPrice(): string
    {
        return '$'.number_format((float) $this->price, 0);
    }

    public function formattedMileage(): ?string
    {
        if ($this->mileage === null) {
            return null;
        }

        return number_format($this->mileage).' Miles';
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('status', 'available')->where('is_visible', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('featured', true);
    }

    public function scopePinnedFirst(Builder $query): Builder
    {
        return $query->orderByDesc('is_pinned')->orderByDesc('year')->orderBy('price');
    }

    public function scopeSearch(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['q'] ?? null, function (Builder $query, string $q) {
                $query->where(function (Builder $query) use ($q) {
                    $query->where('description', 'like', "%{$q}%")
                        ->orWhere('description_es', 'like', "%{$q}%")
                        ->orWhere('color', 'like', "%{$q}%")
                        ->orWhere('vin', 'like', "%{$q}%")
                        ->orWhere('year', 'like', "%{$q}%")
                        ->orWhereHas('brand', fn (Builder $brand) => $brand->where('name', 'like', "%{$q}%"))
                        ->orWhereHas('vehicleModel', fn (Builder $model) => $model->where('name', 'like', "%{$q}%"));
                });
            })
            ->when($filters['brand_id'] ?? null, fn (Builder $query, $brandId) => $query->where('brand_id', $brandId))
            ->when($filters['vehicle_model_id'] ?? null, fn (Builder $query, $modelId) => $query->where('vehicle_model_id', $modelId))
            ->when($filters['year_from'] ?? null, fn (Builder $query, $year) => $query->where('year', '>=', $year))
            ->when($filters['year_to'] ?? null, fn (Builder $query, $year) => $query->where('year', '<=', $year))
            ->when($filters['price_min'] ?? null, fn (Builder $query, $price) => $query->where('price', '>=', $price))
            ->when($filters['price_max'] ?? null, fn (Builder $query, $price) => $query->where('price', '<=', $price))
            ->when($filters['seats'] ?? null, fn (Builder $query, $seats) => $query->where('seats', '>=', $seats))
            ->when($filters['engine_type'] ?? null, fn (Builder $query, $engine) => $query->where('engine_type', $engine))
            ->when($filters['transmission'] ?? null, fn (Builder $query, $transmission) => $query->where('transmission', $transmission))
            ->when($filters['body_type'] ?? null, fn (Builder $query, $body) => $query->where('body_type', $body));
    }
}
