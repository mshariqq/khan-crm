<?php

namespace Tests\Feature;

use App\Models\Currency;
use App\Models\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CurrencyModuleTest extends TestCase
{
    use RefreshDatabase;

    protected Staff $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $this->admin = Staff::where('email', 'admin@crm.com')->first();
    }

    protected function authenticateAsAdmin(): void
    {
        $this->actingAs($this->admin, 'staff');
    }

    protected function createCurrency(array $overrides = []): Currency
    {
        return Currency::create(array_merge([
            'name' => 'US Dollar',
            'symbol' => '$',
            'decimal_separator' => '.',
            'thousand_separator' => ',',
            'placement' => 'before',
            'isdefault' => 0,
        ], $overrides));
    }

    public function test_guest_is_redirected_to_login_when_accessing_currencies(): void
    {
        $this->get(route('admin.currencies.index'))->assertRedirect(route('login.staff'));
        $this->get(route('admin.currencies.create'))->assertRedirect(route('login.staff'));
        $this->post(route('admin.currencies.store'), [])->assertRedirect(route('login.staff'));
    }

    public function test_admin_can_view_currencies_list(): void
    {
        $this->authenticateAsAdmin();
        $this->createCurrency(['name' => 'Euro', 'symbol' => '€']);

        $response = $this->get(route('admin.currencies.index'));
        $response->assertStatus(200);
        $response->assertSee('Euro');
    }

    public function test_admin_can_create_currency(): void
    {
        $this->authenticateAsAdmin();

        $response = $this->post(route('admin.currencies.store'), [
            'symbol' => '£',
            'name' => 'Pound Sterling',
            'decimal_separator' => '.',
            'thousand_separator' => ',',
            'placement' => 'before',
        ]);

        $response->assertRedirect(route('admin.currencies.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tblcurrencies', [
            'name' => 'Pound Sterling',
            'symbol' => '£',
        ]);
    }

    public function test_create_currency_requires_required_fields(): void
    {
        $this->authenticateAsAdmin();

        $response = $this->post(route('admin.currencies.store'), []);
        $response->assertSessionHasErrors(['symbol', 'name']);
    }

    public function test_admin_can_update_currency(): void
    {
        $this->authenticateAsAdmin();
        $currency = $this->createCurrency();

        $response = $this->put(route('admin.currencies.update', $currency->id), [
            'symbol' => '$',
            'name' => 'Canadian Dollar',
            'decimal_separator' => '.',
            'thousand_separator' => ',',
            'placement' => 'before',
            'isdefault' => 0,
        ]);

        $response->assertRedirect(route('admin.currencies.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tblcurrencies', [
            'id' => $currency->id,
            'name' => 'Canadian Dollar',
        ]);
    }

    public function test_admin_can_delete_currency(): void
    {
        $this->authenticateAsAdmin();
        $currency = $this->createCurrency();

        $response = $this->delete(route('admin.currencies.destroy', $currency->id));

        $response->assertRedirect(route('admin.currencies.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('tblcurrencies', ['id' => $currency->id]);
    }
}
