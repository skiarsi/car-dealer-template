<?php

namespace Database\Factories;

use App\Models\Inquiry;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Inquiry>
 */
class InquiryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'vehicle_id' => Vehicle::factory(),
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->numerify('+1 ### ### ####'),
            'message' => fake()->optional()->sentence(),
            'status' => 'new',
        ];
    }
}
