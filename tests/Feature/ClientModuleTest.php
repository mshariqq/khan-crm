<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Contact;
use App\Models\Country;
use App\Models\Currency;
use App\Models\CustomersGroup;
use App\Models\Project;
use App\Models\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientModuleTest extends TestCase
{
    use RefreshDatabase;

    protected Staff $admin;
    protected Staff $staffMember;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\DatabaseSeeder::class);

        $this->admin = Staff::where('email', 'admin@crm.com')->first();

        $this->staffMember = Staff::create([
            'firstname' => 'Test',
            'lastname' => 'Staff',
            'email' => 'teststaff@crm.com',
            'password' => bcrypt('password'),
            'role' => 6,
            'active' => 1,
            'datecreated' => now(),
            'default_language' => 'english',
            'direction' => 'ltr',
        ]);
    }

    protected function authenticateAsAdmin(): void
    {
        $this->actingAs($this->admin, 'staff');
    }

    protected function authenticateAsStaff(): void
    {
        $this->actingAs($this->staffMember, 'staff');
    }

    protected function createClient(array $overrides = []): Client
    {
        return Client::create(array_merge([
            'company' => 'Default Client',
            'phonenumber' => '1234567890',
            'country' => 1,
            'city' => 'Kabul',
            'zip' => '1001',
            'state' => 'Kabul',
            'address' => 'Test Address',
            'website' => 'https://example.com',
            'datecreated' => now(),
            'active' => 1,
            'default_currency' => 1,
            'customer_group_id' => null,
            'addedfrom' => $this->admin->staffid,
        ], $overrides));
    }

    public function test_guest_is_redirected_to_login_when_accessing_clients(): void
    {
        $this->get(route('admin.clients.index'))->assertRedirect(route('login.staff'));
        $this->get(route('admin.clients.create'))->assertRedirect(route('login.staff'));
        $this->post(route('admin.clients.store'), [])->assertRedirect(route('login.staff'));
    }

    public function test_admin_can_view_clients_list(): void
    {
        $this->authenticateAsAdmin();
        $this->createClient(['company' => 'Visible Client Co.']);

        $response = $this->get(route('admin.clients.index'));
        $response->assertStatus(200);
        $response->assertSee('Visible Client Co.');
    }

    public function test_admin_can_create_client(): void
    {
        $this->authenticateAsAdmin();

        $response = $this->post(route('admin.clients.store'), [
            'company' => 'New Client Inc.',
            'phonenumber' => '9876543210',
            'country' => 1,
            'city' => 'Kabul',
            'zip' => '1001',
            'state' => 'Kabul',
            'address' => '123 Main St',
            'website' => 'https://newclient.com',
            'default_currency' => 1,
            'customer_group_id' => null,
        ]);

        $response->assertRedirect(route('admin.clients.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tblclients', [
            'company' => 'New Client Inc.',
            'phonenumber' => '9876543210',
            'active' => 1,
        ]);
    }

    public function test_create_client_requires_required_fields(): void
    {
        $this->authenticateAsAdmin();

        $response = $this->post(route('admin.clients.store'), [
            'company' => '',
        ]);

        $response->assertSessionHasErrors(['company', 'country', 'default_currency']);
    }

    public function test_admin_can_view_single_client(): void
    {
        $this->authenticateAsAdmin();
        $client = $this->createClient(['company' => 'Single Client Co.']);

        $response = $this->get(route('admin.clients.show', $client->userid));
        $response->assertStatus(200);
        $response->assertSee('Single Client Co.');
    }

    public function test_admin_can_update_client(): void
    {
        $this->authenticateAsAdmin();
        $client = $this->createClient(['company' => 'Old Name Co.']);

        $response = $this->put(route('admin.clients.update', $client->userid), [
            'company' => 'Updated Name Co.',
            'phonenumber' => '5551234567',
            'country' => 1,
            'city' => 'Herat',
            'zip' => '3001',
            'state' => 'Herat',
            'address' => '456 Updated St',
            'website' => 'https://updated.com',
            'default_currency' => 1,
            'customer_group_id' => null,
        ]);

        $response->assertRedirect(route('admin.clients.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tblclients', [
            'userid' => $client->userid,
            'company' => 'Updated Name Co.',
            'city' => 'Herat',
        ]);
    }

    public function test_admin_can_delete_client_without_dependencies(): void
    {
        $this->authenticateAsAdmin();
        $client = $this->createClient();

        $response = $this->delete(route('admin.clients.destroy', $client->userid));

        $response->assertRedirect(route('admin.clients.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('tblclients', ['userid' => $client->userid]);
    }

    public function test_admin_cannot_delete_client_with_projects(): void
    {
        $this->authenticateAsAdmin();
        $client = $this->createClient();

        Project::create([
            'name' => 'Dependent Project',
            'description' => 'This project blocks client deletion',
            'status' => 1,
            'clientid' => $client->userid,
            'billing_type' => 1,
            'start_date' => now()->format('Y-m-d'),
            'deadline' => now()->addDays(30)->format('Y-m-d'),
            'project_created' => now()->format('Y-m-d'),
            'project_cost' => 1000.00,
            'project_rate_per_hour' => 50.00,
            'estimated_hours' => 20,
            'addedfrom' => $this->admin->staffid,
            'progress' => 0,
        ]);

        $response = $this->delete(route('admin.clients.destroy', $client->userid));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('tblclients', ['userid' => $client->userid]);
    }

    public function test_staff_without_permission_cannot_create_clients(): void
    {
        $this->authenticateAsStaff();

        $response = $this->post(route('admin.clients.store'), [
            'company' => 'Should Not Create',
            'country' => 1,
            'default_currency' => 1,
        ]);

        $response->assertStatus(403);
    }

    public function test_search_clients_api_returns_json(): void
    {
        $this->authenticateAsAdmin();
        $this->createClient(['company' => 'Searchable Client Corp']);

        $response = $this->getJson(route('admin.clients.search', ['q' => 'Searchable']));

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['company' => 'Searchable Client Corp']);
    }

    public function test_search_clients_api_requires_minimum_3_chars(): void
    {
        $this->authenticateAsAdmin();

        $response = $this->getJson(route('admin.clients.search', ['q' => 'ab']));

        $response->assertStatus(200);
        $this->assertEmpty($response->json());
    }

    public function test_filter_clients_by_status(): void
    {
        $this->authenticateAsAdmin();
        $this->createClient(['company' => 'Active Client', 'active' => 1]);
        $this->createClient(['company' => 'Inactive Client', 'active' => 0]);

        $response = $this->get(route('admin.clients.index', ['status' => 1]));
        $response->assertStatus(200);
        $response->assertSee('Active Client');
        $response->assertDontSee('Inactive Client');

        $response = $this->get(route('admin.clients.index', ['status' => 0]));
        $response->assertStatus(200);
        $response->assertSee('Inactive Client');
        $response->assertDontSee('Active Client');
    }

    public function test_filter_clients_by_country(): void
    {
        $this->authenticateAsAdmin();
        $this->createClient(['company' => 'US Client', 'country' => 1]);
        $this->createClient(['company' => 'DE Client', 'country' => 2]);

        $response = $this->get(route('admin.clients.index', ['country' => 1]));
        $response->assertStatus(200);
        $response->assertSee('US Client');
        $response->assertDontSee('DE Client');
    }

    public function test_filter_clients_by_date_range(): void
    {
        $this->authenticateAsAdmin();
        $client = $this->createClient(['company' => 'Dated Client']);

        $response = $this->get(route('admin.clients.index', [
            'date_from' => now()->subDay()->format('Y-m-d'),
            'date_to' => now()->addDay()->format('Y-m-d'),
        ]));
        $response->assertStatus(200);
        $response->assertSee('Dated Client');
    }

    public function test_admin_can_view_client_contacts_tab(): void
    {
        $this->authenticateAsAdmin();
        $client = $this->createClient();

        Contact::create([
            'userid' => $client->userid,
            'is_primary' => 1,
            'firstname' => 'Jane',
            'lastname' => 'Doe',
            'email' => 'jane@example.com',
            'phonenumber' => '5550001111',
            'password' => bcrypt('password'),
            'datecreated' => now(),
            'active' => 1,
        ]);

        $response = $this->get(route('admin.clients.contacts', $client->userid));
        $response->assertStatus(200);
        $response->assertSee('Jane');
        $response->assertSee('Doe');
    }
}
