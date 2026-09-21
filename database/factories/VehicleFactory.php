<?php

namespace Database\Factories;

use App\Models\Vehicle;
use App\Models\VehicleModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Vehicle>
 */
class VehicleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'vehicle_model_id' => VehicleModel::factory(),
            'brand_id' => fn (array $attributes) => VehicleModel::query()->find($attributes['vehicle_model_id'])->brand_id,
            'slug' => Str::slug(fake()->unique()->bothify('veh-####-????')),
            'year' => fake()->numberBetween(2018, 2025),
            'price' => fake()->numberBetween(12000, 62000),
            'mileage' => fake()->numberBetween(5000, 90000),
            'engine_type' => fake()->randomElement(['gasoline', 'diesel', 'hybrid', 'electric']),
            'transmission' => fake()->randomElement(['automatic', 'manual']),
            'seats' => fake()->randomElement([2, 4, 5, 7]),
            'color' => fake()->randomElement(['White', 'Black', 'Silver', 'Blue', 'Gray']),
            'body_type' => fake()->randomElement(['sedan', 'suv', 'hatchback', 'truck', 'coupe', 'van']),
            'drivetrain' => fake()->randomElement(['fwd', 'rwd', 'awd']),
            'doors' => fake()->randomElement([2, 4, 5]),
            'description' => fake()->sentence(12),
            'description_es' => fake()->sentence(12),
            'status' => 'available',
            'featured' => false,
            'is_visible' => true,
            'is_pinned' => false,
            'vin' => strtoupper(fake()->unique()->bothify('??##############')),
        ];
    }
}
