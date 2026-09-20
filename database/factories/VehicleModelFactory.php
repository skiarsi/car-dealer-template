<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\VehicleModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<VehicleModel>
 */
class VehicleModelFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->bothify('Model-###');

        return [
            'brand_id' => Brand::factory(),
            'name' => $name,
            'slug' => Str::slug($name),
        ];
    }
}
