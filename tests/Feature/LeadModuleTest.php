<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Country;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\LeadStatus;
use App\Models\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadModuleTest extends TestCase
{
    use RefreshDatabase;

    protected Staff $admin;
    protected LeadStatus $status1;
    protected LeadStatus $status2;
    protected LeadStatus $status3;
    protected LeadSource $source1;
    protected LeadSource $source2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\DatabaseSeeder::class);

        $this->admin = Staff::where('email', 'admin@crm.com')->first();

        // Seed extra lead statuses for filter and transition tests
        $this->status1 = LeadStatus::where('id', 1)->first() ?? LeadStatus::create(['id' => 1, 'name' => 'Customer', 'statusorder' => 1000, 'color' => '#7cb342', 'isdefault' => 1]);
        $this->status2 = LeadStatus::create(['name' => 'Contacted', 'statusorder' => 1, 'color' => '#ffc107', 'isdefault' => 0]);
        $this->status3 = LeadStatus::create(['name' => 'Qualified', 'statusorder' => 2, 'color' => '#17a2b8', 'isdefault' => 0]);

        // Sources are seeded by LeadSourcesSeeder
        $this->source1 = LeadSource::where('id', 1)->first() ?? LeadSource::create(['id' => 1, 'name' => 'Google']);
        $this->source2 = LeadSource::where('id', 2)->first() ?? LeadSource::create(['id' => 2, 'name' => 'Facebook']);

        // Ensure country exists for filter tests
        if (!Country::where('country_id', 1)->exists()) {
            Country::create(['country_id' => 1, 'long_name' => 'United States', 'iso2' => 'US', 'iso3' => 'USA', 'numcode' => 840]);
        }
        if (!Country::where('country_id', 2)->exists()) {
            Country::create(['country_id' => 2, 'long_name' => 'Canada', 'iso2' => 'CA', 'iso3' => 'CAN', 'numcode' => 124]);
        }
    }

    protected function authenticateAsAdmin(): void
    {
        $this->actingAs($this->admin, 'staff');
    }

    protected function createLead(array $overrides = []): Lead
    {
        return Lead::create(array_merge([
            'name' => 'Test Lead',
            'email' => 'test@example.com',
            'company' => 'Test Corp',
            'phonenumber' => '1234567890',
            'status' => $this->status1->id,
            'source' => $this->source1->id,
            'country' => 1,
            'lead_value' => 1000.00,
            'description' => 'Test description',
            'dateadded' => now(),
            'addedfrom' => $this->admin->staffid,
        ], $overrides));
    }

    public function test_guest_is_redirected_to_login_when_accessing_leads(): void
    {
        $this->get(route('admin.leads.index'))->assertRedirect(route('login.staff'));
        $this->get(route('admin.leads.create'))->assertRedirect(route('login.staff'));
        $this->post(route('admin.leads.store'), [])->assertRedirect(route('login.staff'));
    }

    public function test_admin_can_view_leads_list(): void
    {
        $this->authenticateAsAdmin();
        $this->createLead(['name' => 'John Doe']);

        $response = $this->get(route('admin.leads.index'));
        $response->assertStatus(200);
        $response->assertSee('John Doe');
        $response->assertSee('test@example.com');
        $response->assertSee('Test Corp');
        $response->assertSee($this->status1->name);
        $response->assertSee($this->source1->name);
        $response->assertSee('1,000.00');
    }

    public function test_admin_can_create_lead(): void
    {
        $this->authenticateAsAdmin();

        $response = $this->post(route('admin.leads.store'), [
            'name' => 'New Lead',
            'email' => 'newlead@example.com',
            'phonenumber' => '5551234567',
            'company' => 'New Corp',
            'status' => $this->status1->id,
            'source' => $this->source1->id,
            'country' => 1,
            'lead_value' => 500.00,
            'description' => 'A new potential client',
        ]);

        $response->assertRedirect(route('admin.leads.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tblleads', [
            'name' => 'New Lead',
            'email' => 'newlead@example.com',
            'company' => 'New Corp',
            'lead_value' => 500.00,
        ]);
    }

    public function test_create_lead_requires_required_fields(): void
    {
        $this->authenticateAsAdmin();

        $response = $this->post(route('admin.leads.store'), []);
        $response->assertSessionHasErrors(['name', 'status', 'source']);
    }

    public function test_admin_can_view_single_lead(): void
    {
        $this->authenticateAsAdmin();
        $lead = $this->createLead(['name' => 'Single Lead View']);

        $response = $this->get(route('admin.leads.show', $lead->id));
        $response->assertStatus(200);
        $response->assertSee('Single Lead View');
        $response->assertSee('test@example.com');
        $response->assertSee('Test Corp');
        $response->assertSee('$1,000.00');
        $response->assertSee($this->status1->name);
    }

    public function test_admin_can_update_lead(): void
    {
        $this->authenticateAsAdmin();
        $lead = $this->createLead();

        $response = $this->put(route('admin.leads.update', $lead->id), [
            'name' => 'Updated Lead',
            'email' => 'updated@example.com',
            'phonenumber' => '9998887777',
            'company' => 'Updated Corp',
            'status' => $this->status2->id,
            'source' => $this->source2->id,
            'country' => 2,
            'lead_value' => 2500.00,
        ]);

        $response->assertRedirect(route('admin.leads.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tblleads', [
            'id' => $lead->id,
            'name' => 'Updated Lead',
            'email' => 'updated@example.com',
            'company' => 'Updated Corp',
            'status' => $this->status2->id,
            'source' => $this->source2->id,
            'lead_value' => 2500.00,
        ]);
    }

    public function test_admin_can_delete_lead(): void
    {
        $this->authenticateAsAdmin();
        $lead = $this->createLead();

        $response = $this->delete(route('admin.leads.destroy', $lead->id));

        $response->assertRedirect(route('admin.leads.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('tblleads', ['id' => $lead->id]);
    }

    public function test_filter_leads_by_status(): void
    {
        $this->authenticateAsAdmin();
        $lead1 = $this->createLead(['name' => 'Status A Lead', 'status' => $this->status1->id]);
        $lead2 = $this->createLead(['name' => 'Status B Lead', 'status' => $this->status2->id]);

        $response = $this->get(route('admin.leads.index', ['status' => $this->status2->id]));
        $response->assertStatus(200);
        $response->assertSee('Status B Lead');
        $response->assertDontSee('Status A Lead');
    }

    public function test_filter_leads_by_source(): void
    {
        $this->authenticateAsAdmin();
        $lead1 = $this->createLead(['name' => 'Source A Lead', 'source' => $this->source1->id]);
        $lead2 = $this->createLead(['name' => 'Source B Lead', 'source' => $this->source2->id]);

        $response = $this->get(route('admin.leads.index', ['source' => $this->source2->id]));
        $response->assertStatus(200);
        $response->assertSee('Source B Lead');
        $response->assertDontSee('Source A Lead');
    }

    public function test_filter_leads_by_country(): void
    {
        $this->authenticateAsAdmin();
        $lead1 = $this->createLead(['name' => 'US Lead', 'country' => 1]);
        $lead2 = $this->createLead(['name' => 'Canada Lead', 'country' => 2]);

        $response = $this->get(route('admin.leads.index', ['country' => 2]));
        $response->assertStatus(200);
        $response->assertSee('Canada Lead');
        $response->assertDontSee('US Lead');
    }

    public function test_admin_can_store_note_on_lead(): void
    {
        $this->authenticateAsAdmin();
        $lead = $this->createLead();

        $response = $this->post(route('admin.leads.notes.store', $lead->id), [
            'description' => 'This is a test note',
        ]);

        $response->assertRedirect(route('admin.leads.show', $lead->id));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tblnotes', [
            'description' => 'This is a test note',
            'rel_id' => $lead->id,
            'rel_type' => 'lead',
        ]);
    }

    public function test_note_requires_description(): void
    {
        $this->authenticateAsAdmin();
        $lead = $this->createLead();

        $response = $this->post(route('admin.leads.notes.store', $lead->id), []);
        $response->assertSessionHasErrors(['description']);
    }

    public function test_admin_can_store_reminder_on_lead(): void
    {
        $this->authenticateAsAdmin();
        $lead = $this->createLead();
        $reminderDate = now()->addDays(7)->format('Y-m-d');

        $response = $this->post(route('admin.leads.reminders.store', $lead->id), [
            'description' => 'Follow up with lead',
            'date' => $reminderDate,
        ]);

        $response->assertRedirect(route('admin.leads.show', $lead->id));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tblreminders', [
            'description' => 'Follow up with lead',
            'date' => $reminderDate,
            'rel_id' => $lead->id,
            'rel_type' => 'lead',
        ]);
    }

    public function test_reminder_requires_required_fields(): void
    {
        $this->authenticateAsAdmin();
        $lead = $this->createLead();

        $response = $this->post(route('admin.leads.reminders.store', $lead->id), []);
        $response->assertSessionHasErrors(['description', 'date']);
    }

    public function test_admin_can_mark_lead_as_lost(): void
    {
        $this->authenticateAsAdmin();
        $lead = $this->createLead();

        $response = $this->post(route('admin.leads.mark_lost', $lead->id));

        $response->assertRedirect(route('admin.leads.show', $lead->id));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tblleads', [
            'id' => $lead->id,
            'lost' => 1,
            'status' => 6,
        ]);
    }

    public function test_admin_can_mark_lead_as_junk(): void
    {
        $this->authenticateAsAdmin();
        $lead = $this->createLead();

        $response = $this->post(route('admin.leads.mark_junk', $lead->id));

        $response->assertRedirect(route('admin.leads.show', $lead->id));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tblleads', [
            'id' => $lead->id,
            'junk' => 1,
            'status' => 7,
        ]);
    }

    public function test_admin_can_convert_lead_to_client(): void
    {
        $this->authenticateAsAdmin();
        $lead = $this->createLead();

        $response = $this->post(route('admin.leads.convert', $lead->id), [
            'company' => 'Converted Client LLC',
            'email' => 'client@example.com',
        ]);

        $client = Client::where('company', 'Converted Client LLC')->first();
        $this->assertNotNull($client);

        $response->assertRedirect(route('admin.clients.show', $client->userid));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tblclients', [
            'company' => 'Converted Client LLC',
        ]);

        $this->assertDatabaseHas('tblleads', [
            'id' => $lead->id,
            'client_id' => $client->userid,
            'status' => 5,
        ]);
    }

    public function test_admin_can_submit_gdpr_request(): void
    {
        $this->authenticateAsAdmin();
        $lead = $this->createLead();

        $response = $this->post(route('admin.leads.gdpr.store', $lead->id), [
            'type' => 'export',
            'description' => 'Please export my data',
        ]);

        $response->assertRedirect(route('admin.leads.show', $lead->id));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tblgdpr_requests', [
            'lead_id' => $lead->id,
            'request_type' => 'export',
            'description' => 'Please export my data',
            'status' => 'pending',
        ]);
    }

    public function test_gdpr_requires_valid_type(): void
    {
        $this->authenticateAsAdmin();
        $lead = $this->createLead();

        $response = $this->post(route('admin.leads.gdpr.store', $lead->id), [
            'type' => 'invalid-type',
        ]);
        $response->assertSessionHasErrors(['type']);
    }

    public function test_converted_lead_shows_client_link_in_sidebar(): void
    {
        $this->authenticateAsAdmin();
        $lead = $this->createLead();

        $this->post(route('admin.leads.convert', $lead->id), [
            'company' => 'Linked Client',
            'email' => 'linked@example.com',
        ]);

        $response = $this->get(route('admin.leads.show', $lead->id));
        $response->assertStatus(200);
        $response->assertSee('View Client');
    }
}
