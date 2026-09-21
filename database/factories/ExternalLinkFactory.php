<?php

namespace Database\Factories;

use App\Models\ExternalLink;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ExternalLink>
 */
class ExternalLinkFactory extends Factory
{
    public function definition(): array
    {
        return [
            'label' => 'Instagram',
            'url' => 'https://instagram.com/apexmotors',
            'platform' => 'instagram',
            'sort_order' => 0,
            'is_visible' => true,
        ];
    }
}
