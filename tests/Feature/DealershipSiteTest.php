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
            ->assertDontSee($other->vehicleModel->name)
            ->assertSee(__('search.filters'));
    }

    public function test_inventory_filters_year_and_price_with_range_sliders(): void
    {
        $oldCheap = Vehicle::factory()->create(['year' => 2015, 'price' => 8000]);
        $newExpensive = Vehicle::factory()->create(['year' => 2024, 'price' => 45000]);

        $this->get(route('inventory'))
            ->assertOk()
            ->assertSee('dual-range', false)
            ->assertSee(__('search.year'))
            ->assertSee(__('search.price'));

        Livewire::test('inventory')
            ->call('setYearRange', 2020, 2025)
            ->assertSet('year_from', '2020')
            ->assertSet('year_to', '2025')
            ->assertSee($newExpensive->slug)
            ->assertDontSee($oldCheap->slug);

        Livewire::test('inventory')
            ->call('setPriceRange', 5000, 10000)
            ->assertSet('price_min', '5000')
            ->assertSet('price_max', '10000')
            ->assertSee($oldCheap->slug)
            ->assertDontSee($newExpensive->slug);
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
            ->set('message', 'Need details')
            ->call('submit')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('inquiries', [
            'vehicle_id' => $vehicle->id,
            'name' => 'Alex Rivera',
            'email' => 'alex@example.com',
            'message' => 'Need details',
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

    public function test_admin_can_open_an_inquiry_message_in_a_modal(): void
    {
        $user = User::factory()->create();
        $inquiry = Inquiry::factory()->create([
            'name' => 'Chris Park',
            'message' => 'Can I see this car on Saturday?',
        ]);

        $this->actingAs($user);

        Livewire::test('admin.inquiry-modal')
            ->dispatch('inquiry-open', id: $inquiry->id)
            ->assertSet('open', true)
            ->assertSee('Can I see this car on Saturday?');

        $this->assertSame('read', $inquiry->fresh()->status);
    }

    public function test_hidden_vehicles_are_not_listed_publicly(): void
    {
        $hidden = Vehicle::factory()->create(['is_visible' => false, 'year' => 2011]);
        $visible = Vehicle::factory()->create(['is_visible' => true, 'year' => 2024]);

        Livewire::test('inventory')
            ->assertSee($visible->slug)
            ->assertDontSee($hidden->slug);

        $this->get(route('vehicles.show', $hidden))->assertNotFound();
        $this->get(route('vehicles.show', $visible))->assertOk();
    }

    public function test_pinned_vehicles_appear_first_in_inventory(): void
    {
        $later = Vehicle::factory()->create(['year' => 2025, 'price' => 10000, 'is_pinned' => false]);
        $pinned = Vehicle::factory()->create(['year' => 2018, 'price' => 50000, 'is_pinned' => true]);

        Livewire::test('inventory')->assertSeeInOrder([$pinned->slug, $later->slug]);
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
