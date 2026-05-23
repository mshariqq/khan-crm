<?php

namespace Tests\Feature;

use App\Models\PaymentMode;
use App\Models\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentModeModuleTest extends TestCase
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

    protected function createPaymentMode(array $overrides = []): PaymentMode
    {
        return PaymentMode::create(array_merge([
            'name' => 'Bank Transfer',
            'description' => 'Payment via bank transfer',
            'show_on_pdf' => 1,
            'active' => 1,
        ], $overrides));
    }

    public function test_guest_is_redirected_to_login_when_accessing_payment_modes(): void
    {
        $this->get(route('admin.paymentmodes.index'))->assertRedirect(route('login.staff'));
        $this->get(route('admin.paymentmodes.create'))->assertRedirect(route('login.staff'));
        $this->post(route('admin.paymentmodes.store'), [])->assertRedirect(route('login.staff'));
    }

    public function test_admin_can_view_payment_modes_list(): void
    {
        $this->authenticateAsAdmin();
        $this->createPaymentMode(['name' => 'Credit Card']);

        $response = $this->get(route('admin.paymentmodes.index'));
        $response->assertStatus(200);
        $response->assertSee('Credit Card');
    }

    public function test_admin_can_create_payment_mode(): void
    {
        $this->authenticateAsAdmin();

        $response = $this->post(route('admin.paymentmodes.store'), [
            'name' => 'PayPal',
            'description' => 'Online payment via PayPal',
            'show_on_pdf' => 1,
            'active' => 1,
        ]);

        $response->assertRedirect(route('admin.paymentmodes.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tblpayment_modes', [
            'name' => 'PayPal',
            'active' => 1,
        ]);
    }

    public function test_create_payment_mode_requires_name(): void
    {
        $this->authenticateAsAdmin();

        $response = $this->post(route('admin.paymentmodes.store'), []);
        $response->assertSessionHasErrors(['name']);
    }

    public function test_admin_can_update_payment_mode(): void
    {
        $this->authenticateAsAdmin();
        $pm = $this->createPaymentMode();

        $response = $this->put(route('admin.paymentmodes.update', $pm->id), [
            'name' => 'Wire Transfer',
            'description' => 'Updated description',
            'show_on_pdf' => 0,
            'active' => 1,
        ]);

        $response->assertRedirect(route('admin.paymentmodes.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tblpayment_modes', [
            'id' => $pm->id,
            'name' => 'Wire Transfer',
        ]);
    }

    public function test_admin_can_delete_payment_mode(): void
    {
        $this->authenticateAsAdmin();
        $pm = $this->createPaymentMode();

        $response = $this->delete(route('admin.paymentmodes.destroy', $pm->id));

        $response->assertRedirect(route('admin.paymentmodes.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('tblpayment_modes', ['id' => $pm->id]);
    }
}
