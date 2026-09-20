<?php

namespace App\Models;

use App\Support\LocalizesAttributes;
use App\Support\OpeningHours;
use Illuminate\Database\Eloquent\Model;

class Dealership extends Model
{
    use LocalizesAttributes;

    protected $fillable = [
        'name',
        'tagline',
        'tagline_es',
        'about',
        'about_es',
        'phone',
        'email',
        'address',
        'city',
        'hours',
        'hours_es',
        'opening_hours',
    ];

    protected function casts(): array
    {
        return [
            'opening_hours' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $dealership): void {
            if (empty($dealership->opening_hours)) {
                $dealership->opening_hours = OpeningHours::defaults();
            }
        });
    }

    public static function current(): ?self
    {
        return static::query()->first();
    }

    public function weeklyHours(): array
    {
        return OpeningHours::normalize($this->opening_hours);
    }
}
