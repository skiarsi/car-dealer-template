<?php

namespace Tests\Feature;

use App\Models\Dealership;
use App\Models\Inquiry;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DealershipSiteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Dealership::query()->create([
            'name' => 'Apex Motors',
            'tagline' => 'Clear inventory.',
            'tagline_es' => 'Inventario claro.',
            'about' => 'About the dealer',
            'about_es' => 'Sobre el concesionario',
            'phone' => '+1 555 0100',
            'email' => 'hello@apex.test',
            'address' => '1 Main St',
            'city' => 'Miami',
            'opening_hours' => \App\Support\OpeningHours::defaults(),
        ]);
    }

    public function test_home_and_inventory_pages_render(): void
    {
        $vehicle = Vehicle::factory()->create(['featured' => true]);

        $this->get(route('home'))->assertOk()->assertSee('Apex Motors');
        $this->get(route('inventory'))->assertOk()->assertSee($vehicle->brand->name);
        $this->get(route('vehicles.show', $vehicle))->assertOk()->assertSee($vehicle->title());
        $this->get(route('contact'))->assertOk()->assertSee('hello@apex.test');
    }

    public function test_inventory_can_filter_by_brand_and_engine(): void
    {
        $match = Vehicle::factory()->create(['engine_type' => 'hybrid', 'year' => 2024, 'price' => 20000]);
        $other = Vehicle::factory()->create(['engine_type' => 'diesel', 'year' => 2019, 'price' => 9000]);

        Livewire::test('inventory', ['brand_id' => (string) $match->brand_id, 'engine_type' => 'hybrid'])
            ->assertSee($match->vehicleModel->name)
            ->assertDontSee($other->vehicleModel->name);
    }

    public function test_inquiry_requires_email_or_phone_and_is_stored(): void
    {
        $vehicle = Vehicle::factory()->create();

        Livewire::test('vehicle-show', ['vehicle' => $vehicle])
            ->set('name', 'Alex Rivera')
            ->set('message', 'Need details')
            ->call('submit')
            ->assertHasErrors(['email']);

        Livewire::test('vehicle-show', ['vehicle' => $vehicle])
            ->set('name', 'Alex Rivera')
            ->set('email', 'alex@example.com')
            ->set('phone', '555-0101')
            ->call('submit')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('inquiries', [
            'vehicle_id' => $vehicle->id,
            'name' => 'Alex Rivera',
            'email' => 'alex@example.com',
            'status' => 'new',
        ]);
    }

    public function test_locale_cookie_switches_language(): void
    {
        $this->get(route('locale.switch', 'es'))
            ->assertRedirect()
            ->assertCookie('locale');

        $this->withCookie('locale', 'es')
            ->get(route('home'))
            ->assertOk()
            ->assertSee('Inventario');
    }

    public function test_admin_can_see_inquiries_after_login(): void
    {
        $user = User::factory()->create(['email' => 'admin@example.com']);
        $inquiry = Inquiry::factory()->create(['name' => 'Maria Lopez']);

        $this->get(route('dashboard'))->assertRedirect(route('login'));

        $this->actingAs($user)
            ->get(route('admin.inquiries'))
            ->assertOk()
            ->assertSee('Maria Lopez');

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Maria Lopez')
            ->assertSee('1');

        Livewire::actingAs($user)
            ->test('admin.inquiry-count')
            ->assertSee('1');

        $this->assertTrue($inquiry->isNew());
    }

    public function test_legal_pages_and_cookie_banner_are_available(): void
    {
        $this->get(route('legal.privacy'))->assertOk()->assertSee('Privacy Policy');
        $this->get(route('legal.cookies'))->assertOk()->assertSee('Cookie Policy');
        $this->get(route('legal.terms'))->assertOk()->assertSee('Terms of Use');

        Livewire::test('cookie-banner')
            ->assertSet('visible', true)
            ->call('accept')
            ->assertSet('visible', false);
    }

    public function test_vehicle_gallery_switches_the_large_image(): void
    {
        $vehicle = Vehicle::factory()->create();
        $vehicle->images()->create(['path' => 'vehicles/one.jpg', 'sort_order' => 0]);
        $vehicle->images()->create(['path' => 'vehicles/two.jpg', 'sort_order' => 1]);

        Livewire::test('vehicle-show', ['vehicle' => $vehicle->fresh(['images'])])
            ->assertSet('activeImage', 0)
            ->call('selectImage', 1)
            ->assertSet('activeImage', 1);
    }
}
