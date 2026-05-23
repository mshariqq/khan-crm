<?php

namespace Tests\Feature;

use App\Models\Staff;
use App\Models\Tax;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaxModuleTest extends TestCase
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

    protected function createTax(array $overrides = []): Tax
    {
        return Tax::create(array_merge([
            'name' => 'VAT',
            'taxrate' => 20.00,
        ], $overrides));
    }

    public function test_guest_is_redirected_to_login_when_accessing_taxes(): void
    {
        $this->get(route('admin.taxes.index'))->assertRedirect(route('login.staff'));
        $this->get(route('admin.taxes.create'))->assertRedirect(route('login.staff'));
        $this->post(route('admin.taxes.store'), [])->assertRedirect(route('login.staff'));
    }

    public function test_admin_can_view_taxes_list(): void
    {
        $this->authenticateAsAdmin();
        $this->createTax(['name' => 'GST', 'taxrate' => 18.00]);

        $response = $this->get(route('admin.taxes.index'));
        $response->assertStatus(200);
        $response->assertSee('GST');
    }

    public function test_admin_can_create_tax(): void
    {
        $this->authenticateAsAdmin();

        $response = $this->post(route('admin.taxes.store'), [
            'name' => 'Sales Tax',
            'taxrate' => 10.00,
        ]);

        $response->assertRedirect(route('admin.taxes.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tbltaxes', [
            'name' => 'Sales Tax',
            'taxrate' => 10.00,
        ]);
    }

    public function test_create_tax_requires_required_fields(): void
    {
        $this->authenticateAsAdmin();

        $response = $this->post(route('admin.taxes.store'), []);
        $response->assertSessionHasErrors(['name', 'taxrate']);
    }

    public function test_admin_can_view_single_tax(): void
    {
        $this->authenticateAsAdmin();
        $tax = $this->createTax();

        $response = $this->get(route('admin.taxes.edit', $tax->id));
        $response->assertStatus(200);
        $response->assertSee('VAT');
    }

    public function test_admin_can_update_tax(): void
    {
        $this->authenticateAsAdmin();
        $tax = $this->createTax();

        $response = $this->put(route('admin.taxes.update', $tax->id), [
            'name' => 'Updated VAT',
            'taxrate' => 15.00,
        ]);

        $response->assertRedirect(route('admin.taxes.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tbltaxes', [
            'id' => $tax->id,
            'name' => 'Updated VAT',
            'taxrate' => 15.00,
        ]);
    }

    public function test_admin_can_delete_tax(): void
    {
        $this->authenticateAsAdmin();
        $tax = $this->createTax();

        $response = $this->delete(route('admin.taxes.destroy', $tax->id));

        $response->assertRedirect(route('admin.taxes.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('tbltaxes', ['id' => $tax->id]);
    }
}
