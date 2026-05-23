<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Contact;
use App\Models\Estimate;
use App\Models\Invoice;
use App\Models\Itemable;
use App\Models\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EstimateModuleTest extends TestCase
{
    use RefreshDatabase;

    protected Staff $admin;
    protected Client $client;
    protected Contact $contact;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\DatabaseSeeder::class);

        $this->admin = Staff::where('email', 'admin@crm.com')->first();

        $this->client = Client::create([
            'company' => 'Estimate Client',
            'phonenumber' => '1234567890',
            'country' => 1,
            'datecreated' => now(),
            'active' => 1,
            'default_currency' => 1,
            'addedfrom' => $this->admin->staffid,
        ]);

        $this->contact = Contact::create([
            'userid' => $this->client->userid,
            'is_primary' => 1,
            'firstname' => 'Estimate',
            'lastname' => 'Client',
            'email' => 'estimate.client@example.com',
            'phonenumber' => '5550001111',
            'password' => bcrypt('password'),
            'datecreated' => now(),
            'active' => 1,
        ]);
    }

    protected function authenticateAsAdmin(): void
    {
        $this->actingAs($this->admin, 'staff');
    }

    protected function createEstimate(array $overrides = []): Estimate
    {
        return Estimate::create(array_merge([
            'number' => 1,
            'clientid' => $this->client->userid,
            'currency' => 1,
            'date' => now()->format('Y-m-d'),
            'datecreated' => now(),
            'subtotal' => 2000.00,
            'total' => 2000.00,
            'status' => 1,
            'addedfrom' => $this->admin->staffid,
            'hash' => \Illuminate\Support\Str::random(32),
            'sent' => 0,
        ], $overrides));
    }

    public function test_guest_is_redirected_to_login_when_accessing_estimates(): void
    {
        $this->get(route('admin.estimates.index'))->assertRedirect(route('login.staff'));
        $this->get(route('admin.estimates.create'))->assertRedirect(route('login.staff'));
        $this->post(route('admin.estimates.store'), [])->assertRedirect(route('login.staff'));
    }

    public function test_admin_can_view_estimates_list(): void
    {
        $this->authenticateAsAdmin();
        $this->createEstimate();

        $response = $this->get(route('admin.estimates.index'));
        $response->assertStatus(200);
        $response->assertSee('1');
        $response->assertSee('Estimate Client');
        $response->assertSee('Draft');
    }

    public function test_admin_can_create_estimate(): void
    {
        $this->authenticateAsAdmin();

        $response = $this->post(route('admin.estimates.store'), [
            'number' => '10',
            'clientid' => $this->client->userid,
            'currency' => 1,
            'date' => now()->format('Y-m-d'),
            'subtotal' => 3000.00,
            'total' => 3000.00,
            'status' => 1,
        ]);

        $response->assertRedirect(route('admin.estimates.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tblestimates', [
            'clientid' => $this->client->userid,
            'total' => 3000.00,
            'status' => 1,
        ]);
    }

    public function test_create_estimate_requires_required_fields(): void
    {
        $this->authenticateAsAdmin();

        $response = $this->post(route('admin.estimates.store'), []);
        $response->assertSessionHasErrors(['number', 'clientid', 'currency', 'date', 'total', 'status']);
    }

    public function test_admin_can_view_single_estimate(): void
    {
        $this->authenticateAsAdmin();
        $estimate = $this->createEstimate();

        $response = $this->get(route('admin.estimates.show', $estimate->id));
        $response->assertStatus(200);
        $response->assertSee('1');
        $response->assertSee('Estimate Client');
    }

    public function test_admin_can_update_estimate(): void
    {
        $this->authenticateAsAdmin();
        $estimate = $this->createEstimate();

        $response = $this->put(route('admin.estimates.update', $estimate->id), [
            'number' => '2',
            'clientid' => $this->client->userid,
            'currency' => 1,
            'date' => now()->format('Y-m-d'),
            'subtotal' => 2500.00,
            'total' => 2500.00,
            'status' => 1,
        ]);

        $response->assertRedirect(route('admin.estimates.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tblestimates', [
            'id' => $estimate->id,
            'total' => 2500.00,
        ]);
    }

    public function test_admin_can_delete_estimate(): void
    {
        $this->authenticateAsAdmin();
        $estimate = $this->createEstimate();

        $response = $this->delete(route('admin.estimates.destroy', $estimate->id));

        $response->assertRedirect(route('admin.estimates.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('tblestimates', ['id' => $estimate->id]);
    }

    public function test_admin_can_mark_estimate_as_accepted(): void
    {
        $this->authenticateAsAdmin();
        $estimate = $this->createEstimate(['status' => 1]);

        $response = $this->post(route('admin.estimates.mark_accepted', $estimate->id));

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tblestimates', [
            'id' => $estimate->id,
            'status' => 3,
        ]);

        $showResponse = $this->get(route('admin.estimates.show', $estimate->id));
        $showResponse->assertSee('Accepted');
    }

    public function test_admin_can_mark_estimate_as_declined(): void
    {
        $this->authenticateAsAdmin();
        $estimate = $this->createEstimate(['status' => 1]);

        $response = $this->post(route('admin.estimates.mark_declined', $estimate->id));

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tblestimates', [
            'id' => $estimate->id,
            'status' => 4,
        ]);

        $showResponse = $this->get(route('admin.estimates.show', $estimate->id));
        $showResponse->assertSee('Declined');
    }

    public function test_admin_can_convert_estimate_to_invoice(): void
    {
        $this->authenticateAsAdmin();
        $estimate = $this->createEstimate(['number' => 42, 'total' => 2000.00, 'subtotal' => 2000.00]);

        $response = $this->post(route('admin.estimates.convert_to_invoice', $estimate->id));

        $invoice = Invoice::where('clientid', $this->client->userid)->first();
        $response->assertRedirect(route('admin.invoices.show', $invoice->id));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tblinvoices', [
            'clientid' => $this->client->userid,
            'total' => 2000.00,
        ]);

        $this->assertDatabaseHas('tblestimates', [
            'id' => $estimate->id,
            'status' => 5,
        ]);
    }

    public function test_filter_estimates_by_status(): void
    {
        $this->authenticateAsAdmin();
        $this->createEstimate(['number' => 9991, 'status' => 1]);
        $this->createEstimate(['number' => 9992, 'status' => 3]);

        $response = $this->get(route('admin.estimates.index', ['status' => 3]));
        $response->assertStatus(200);
        $response->assertSee('9992');
        $response->assertDontSee('9991');
    }
}
