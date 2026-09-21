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

    public function test_admin_can_upload_vehicle_photos_sequentially(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->create();

        $this->actingAs($user);

        $draft = '11111111-1111-1111-1111-111111111111';

        $first = $this->post(route('admin.photos.store'), [
            'photo' => UploadedFile::fake()->image('one.jpg', 1200, 400),
            'draft' => $draft,
        ]);
        $second = $this->post(route('admin.photos.store'), [
            'photo' => UploadedFile::fake()->image('two.png', 900, 600),
            'draft' => $draft,
        ]);
        $third = $this->post(route('admin.photos.store'), [
            'photo' => UploadedFile::fake()->image('three.jpg', 800, 500),
            'draft' => $draft,
        ]);

        $first->assertOk();
        $second->assertOk();
        $third->assertOk();

        Livewire::test('admin.vehicle-form', ['vehicle' => $vehicle])
            ->set('draft', $draft)
            ->call('addPendingPath', $first->json('path'))
            ->call('addPendingPath', $second->json('path'))
            ->call('addPendingPath', $third->json('path'))
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseCount('vehicle_images', 3);
        $vehicle->fresh()->images->each(function ($image): void {
            Storage::disk('public')->assertExists($image->path);
            $info = getimagesizefromstring(Storage::disk('public')->get($image->path));
            $this->assertLessThanOrEqual(800, $info[0]);
            $this->assertSame('image/jpeg', $info['mime']);
        });
    }

    public function test_admin_can_save_dealership_profile_and_hours(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test('admin.hours')
            ->set('name', 'Rivera Autos')
            ->set('tagline', 'Honest cars')
            ->set('tagline_es', 'Autos honestos')
            ->set('about', 'Family owned')
            ->set('about_es', 'Empresa familiar')
            ->set('schedule.sunday.closed', true)
            ->set('schedule.monday.open', '08:30')
            ->set('schedule.monday.close', '17:00')
            ->set('city', 'Tampa')
            ->call('save')
            ->assertHasNoErrors();

        $dealership = Dealership::current();
        $hours = $dealership->weeklyHours();

        $this->assertSame('Rivera Autos', $dealership->name);
        $this->assertSame('Honest cars', $dealership->tagline);
        $this->assertSame('Autos honestos', $dealership->tagline_es);
        $this->assertSame('Family owned', $dealership->about);
        $this->assertTrue($hours['sunday']['closed']);
        $this->assertSame('08:30', $hours['monday']['open']);
        $this->assertSame('17:00', $hours['monday']['close']);
        $this->assertSame('Tampa', $dealership->city);
    }

    public function test_contact_page_lists_weekday_hours(): void
    {
        $this->get(route('contact'))
            ->assertOk()
            ->assertSee('Monday')
            ->assertSee('Closed');
    }

    public function test_admin_can_save_an_external_link_shown_on_the_site(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test('admin.external-links')
            ->set('label', 'Instagram')
            ->set('url', 'https://instagram.com/apexmotors')
            ->set('platform', 'instagram')
            ->set('sort_order', '1')
            ->set('is_visible', true)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('external_links', [
            'label' => 'Instagram',
            'platform' => 'instagram',
            'is_visible' => true,
        ]);

        $this->get(route('home'))->assertOk()->assertSee('Instagram');
        $this->get(route('contact'))->assertOk()->assertSee('Instagram');
    }
}
