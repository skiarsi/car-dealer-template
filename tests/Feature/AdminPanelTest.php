<?php

namespace Tests\Feature;

use App\Models\Dealership;
use App\Models\User;
use App\Models\Vehicle;
use App\Support\OpeningHours;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Dealership::query()->create([
            'name' => 'Apex Motors',
            'phone' => '+1 555 0100',
            'email' => 'hello@apex.test',
            'address' => '1 Main St',
            'city' => 'Miami',
            'opening_hours' => OpeningHours::defaults(),
        ]);
    }

    public function test_admin_can_update_a_vehicle(): void
    {
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['price' => 20000, 'year' => 2020]);

        $this->actingAs($user);

        Livewire::test('admin.vehicle-form', ['vehicle' => $vehicle])
            ->set('price', '21950')
            ->set('year', '2021')
            ->set('description', 'Updated copy')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('vehicles', [
            'id' => $vehicle->id,
            'year' => 2021,
            'description' => 'Updated copy',
        ]);
        $this->assertEquals('21950.00', (string) $vehicle->fresh()->price);
    }

    public function test_admin_can_attach_a_vehicle_photo(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->create();

        $this->actingAs($user);

        Livewire::test('admin.vehicle-form', ['vehicle' => $vehicle])
            ->set('photos', [UploadedFile::fake()->image('car.jpg', 1200, 400)])
            ->call('save')
            ->assertHasNoErrors();

        $path = $vehicle->fresh()->images()->first()->path;
        $this->assertDatabaseCount('vehicle_images', 1);
        Storage::disk('public')->assertExists($path);

        $info = getimagesizefromstring(Storage::disk('public')->get($path));
        $this->assertLessThanOrEqual(800, $info[0]);
        $this->assertSame('image/jpeg', $info['mime']);
    }

    public function test_admin_can_save_weekly_hours(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test('admin.hours')
            ->set('schedule.sunday.closed', true)
            ->set('schedule.monday.open', '08:30')
            ->set('schedule.monday.close', '17:00')
            ->set('city', 'Tampa')
            ->call('save')
            ->assertHasNoErrors();

        $hours = Dealership::current()->weeklyHours();

        $this->assertTrue($hours['sunday']['closed']);
        $this->assertSame('08:30', $hours['monday']['open']);
        $this->assertSame('17:00', $hours['monday']['close']);
        $this->assertSame('Tampa', Dealership::current()->city);
    }

    public function test_contact_page_lists_weekday_hours(): void
    {
        $this->get(route('contact'))
            ->assertOk()
            ->assertSee('Monday')
            ->assertSee('Closed');
    }
}
