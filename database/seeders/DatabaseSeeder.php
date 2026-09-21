<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Dealership;
use App\Models\ExternalLink;
use App\Models\Inquiry;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::factory()->create([
            'name' => 'Dealer Admin',
            'email' => 'admin@example.com',
        ]);

        Dealership::query()->create([
            'name' => 'Apex Motors',
            'tagline' => 'Clear inventory. Honest numbers.',
            'tagline_es' => 'Inventario claro. Precios honestos.',
            'about' => 'Apex Motors is a ready-to-brand dealership template. Swap the business details in the database and the public site updates instantly—no extra photos required.',
            'about_es' => 'Apex Motors es una plantilla lista para personalizar. Cambia los datos del negocio en la base de datos y el sitio se actualiza al instante, sin depender de fotos extra.',
            'phone' => '+1 (305) 555-0148',
            'email' => 'hello@apexmotors.test',
            'address' => '1840 Brickell Avenue',
            'city' => 'Miami, FL',
            'opening_hours' => \App\Support\OpeningHours::defaults(),
        ]);

        ExternalLink::query()->create([
            'label' => 'Instagram',
            'url' => 'https://instagram.com/apexmotors',
            'platform' => 'instagram',
            'sort_order' => 1,
            'is_visible' => true,
        ]);
        ExternalLink::query()->create([
            'label' => 'Facebook',
            'url' => 'https://facebook.com/apexmotors',
            'platform' => 'facebook',
            'sort_order' => 2,
            'is_visible' => true,
        ]);
        ExternalLink::query()->create([
            'label' => 'Google Maps',
            'url' => 'https://maps.google.com/?q=Apex+Motors+Miami',
            'platform' => 'google',
            'sort_order' => 3,
            'is_visible' => true,
        ]);

        $catalog = [
            'Toyota' => ['Camry', 'RAV4', 'Corolla'],
            'Honda' => ['Civic', 'CR-V'],
            'Ford' => ['F-150', 'Escape'],
            'BMW' => ['3 Series', 'X3'],
            'Hyundai' => ['Elantra', 'Tucson'],
        ];

        $models = [];

        foreach ($catalog as $brandName => $modelNames) {
            $brand = Brand::query()->create([
                'name' => $brandName,
                'slug' => Str::slug($brandName),
            ]);

            foreach ($modelNames as $modelName) {
                $models[$brandName.' '.$modelName] = VehicleModel::query()->create([
                    'brand_id' => $brand->id,
                    'name' => $modelName,
                    'slug' => Str::slug($modelName),
                ]);
            }
        }

        $vehicles = [
            ['Toyota', 'Camry', 2023, 24900, 18200, 'gasoline', 'automatic', 5, 'White', 'sedan', 'fwd', 4, true],
            ['Toyota', 'RAV4', 2024, 32850, 9400, 'hybrid', 'automatic', 5, 'Silver', 'suv', 'awd', 5, true],
            ['Toyota', 'Corolla', 2021, 17950, 41200, 'gasoline', 'automatic', 5, 'Blue', 'sedan', 'fwd', 4, false],
            ['Honda', 'Civic', 2022, 21400, 25600, 'gasoline', 'manual', 5, 'Black', 'sedan', 'fwd', 4, false],
            ['Honda', 'CR-V', 2023, 29750, 16300, 'gasoline', 'automatic', 5, 'Gray', 'suv', 'awd', 5, true],
            ['Ford', 'F-150', 2022, 38900, 22100, 'gasoline', 'automatic', 5, 'Black', 'truck', 'rwd', 4, false],
            ['Ford', 'Escape', 2020, 16800, 54800, 'gasoline', 'automatic', 5, 'White', 'suv', 'fwd', 5, false],
            ['BMW', '3 Series', 2021, 33400, 30500, 'gasoline', 'automatic', 5, 'Blue', 'sedan', 'rwd', 4, true],
            ['BMW', 'X3', 2024, 47900, 7800, 'diesel', 'automatic', 5, 'Gray', 'suv', 'awd', 5, false],
            ['Hyundai', 'Elantra', 2022, 18650, 27400, 'gasoline', 'automatic', 5, 'Silver', 'sedan', 'fwd', 4, false],
            ['Hyundai', 'Tucson', 2023, 27200, 14900, 'hybrid', 'automatic', 5, 'White', 'suv', 'awd', 5, false],
            ['Honda', 'Civic', 2019, 14200, 68200, 'gasoline', 'manual', 5, 'Red', 'hatchback', 'fwd', 5, false],
        ];

        $copy = [
            'en' => 'Inspected, fairly priced, and listed with the specs shoppers actually filter by. Ask for a callback if you want the service file or a closer look.',
            'es' => 'Revisado, con precio claro y publicado con los datos que la gente realmente busca. Pide una llamada si quieres el historial o verlo de cerca.',
        ];

        foreach ($vehicles as $index => [$brandName, $modelName, $year, $price, $mileage, $engine, $transmission, $seats, $color, $body, $drive, $doors, $featured]) {
            $model = $models[$brandName.' '.$modelName];

            Vehicle::query()->create([
                'brand_id' => $model->brand_id,
                'vehicle_model_id' => $model->id,
                'slug' => Str::slug($year.' '.$brandName.' '.$modelName.' '.$index),
                'year' => $year,
                'price' => $price,
                'mileage' => $mileage,
                'engine_type' => $engine,
                'transmission' => $transmission,
                'seats' => $seats,
                'color' => $color,
                'body_type' => $body,
                'drivetrain' => $drive,
                'doors' => $doors,
                'description' => $copy['en'],
                'description_es' => $copy['es'],
                'status' => 'available',
                'featured' => $featured,
                'vin' => '1APX'.str_pad((string) ($index + 11), 13, '0', STR_PAD_LEFT),
            ]);
        }

        $sample = Vehicle::query()->where('featured', true)->first();

        Inquiry::query()->create([
            'vehicle_id' => $sample->id,
            'name' => 'Maria Lopez',
            'email' => 'maria@example.com',
            'phone' => '+1 (786) 555-0190',
            'message' => 'Can I schedule a visit this weekend?',
            'status' => 'new',
        ]);
    }
}
