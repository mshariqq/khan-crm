<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Contact;
use App\Models\Proposal;
use App\Models\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProposalModuleTest extends TestCase
{
    use RefreshDatabase;

    protected Staff $admin;
    protected Client $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\DatabaseSeeder::class);

        $this->admin = Staff::where('email', 'admin@crm.com')->first();

        $this->client = Client::create([
            'company' => 'Proposal Client',
            'phonenumber' => '1234567890',
            'country' => 1,
            'datecreated' => now(),
            'active' => 1,
            'default_currency' => 1,
            'addedfrom' => $this->admin->staffid,
        ]);
    }

    protected function authenticateAsAdmin(): void
    {
        $this->actingAs($this->admin, 'staff');
    }

    protected function createProposal(array $overrides = []): Proposal
    {
        return Proposal::create(array_merge([
            'subject' => 'Web Development Proposal',
            'proposal_to' => $this->client->company,
            'currency' => 1,
            'date' => now()->format('Y-m-d'),
            'datecreated' => now(),
            'status' => 1,
            'addedfrom' => $this->admin->staffid,
            'hash' => \Illuminate\Support\Str::random(32),
            'subtotal' => 5000.00,
            'total' => 5000.00,
        ], $overrides));
    }

    public function test_guest_is_redirected_to_login_when_accessing_proposals(): void
    {
        $this->get(route('admin.proposals.index'))->assertRedirect(route('login.staff'));
        $this->get(route('admin.proposals.create'))->assertRedirect(route('login.staff'));
        $this->post(route('admin.proposals.store'), [])->assertRedirect(route('login.staff'));
    }

    public function test_admin_can_view_proposals_list(): void
    {
        $this->authenticateAsAdmin();
        $this->createProposal();

        $response = $this->get(route('admin.proposals.index'));
        $response->assertStatus(200);
        $response->assertSee('Web Development Proposal');
        $response->assertSee($this->client->company);
        $response->assertSee('Open');
    }

    public function test_admin_can_create_proposal(): void
    {
        $this->authenticateAsAdmin();

        $response = $this->post(route('admin.proposals.store'), [
            'subject' => 'SEO Services Proposal',
            'proposal_to' => $this->client->company,
            'currency' => 1,
            'date' => now()->format('Y-m-d'),
            'email' => 'client@example.com',
        ]);

        $response->assertRedirect(route('admin.proposals.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tblproposals', [
            'subject' => 'SEO Services Proposal',
            'proposal_to' => $this->client->company,
        ]);
    }

    public function test_create_proposal_requires_required_fields(): void
    {
        $this->authenticateAsAdmin();

        $response = $this->post(route('admin.proposals.store'), []);
        $response->assertSessionHasErrors(['subject', 'proposal_to', 'currency', 'date']);
    }

    public function test_admin_can_view_single_proposal(): void
    {
        $this->authenticateAsAdmin();
        $proposal = $this->createProposal();

        $response = $this->get(route('admin.proposals.show', $proposal->id));
        $response->assertStatus(200);
        $response->assertSee('Web Development Proposal');
        $response->assertSee($this->client->company);
    }

    public function test_admin_can_update_proposal(): void
    {
        $this->authenticateAsAdmin();
        $proposal = $this->createProposal();

        $response = $this->put(route('admin.proposals.update', $proposal->id), [
            'subject' => 'Updated Proposal Subject',
            'proposal_to' => $this->client->company,
            'currency' => 1,
            'date' => now()->format('Y-m-d'),
        ]);

        $response->assertRedirect(route('admin.proposals.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tblproposals', [
            'id' => $proposal->id,
            'subject' => 'Updated Proposal Subject',
        ]);
    }

    public function test_admin_can_delete_proposal(): void
    {
        $this->authenticateAsAdmin();
        $proposal = $this->createProposal();

        $response = $this->delete(route('admin.proposals.destroy', $proposal->id));

        $response->assertRedirect(route('admin.proposals.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('tblproposals', ['id' => $proposal->id]);
    }

    public function test_admin_can_mark_proposal_as_accepted(): void
    {
        $this->authenticateAsAdmin();
        $proposal = $this->createProposal(['status' => 1]);

        $response = $this->post(route('admin.proposals.mark_accepted', $proposal->id));

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tblproposals', [
            'id' => $proposal->id,
            'status' => 2,
        ]);

        $showResponse = $this->get(route('admin.proposals.show', $proposal->id));
        $showResponse->assertSee('Accepted');
    }

    public function test_admin_can_mark_proposal_as_declined(): void
    {
        $this->authenticateAsAdmin();
        $proposal = $this->createProposal(['status' => 1]);

        $response = $this->post(route('admin.proposals.mark_declined', $proposal->id));

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tblproposals', [
            'id' => $proposal->id,
            'status' => 3,
        ]);

        $showResponse = $this->get(route('admin.proposals.show', $proposal->id));
        $showResponse->assertSee('Declined');
    }

    public function test_admin_can_add_comment_to_proposal(): void
    {
        $this->authenticateAsAdmin();
        $proposal = $this->createProposal();

        $response = $this->post(route('admin.proposals.store_comment', $proposal->id), [
            'content' => 'This is a test comment.',
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tblproposal_comments', [
            'proposalid' => $proposal->id,
            'content' => 'This is a test comment.',
        ]);
    }

    public function test_admin_can_add_note_to_proposal(): void
    {
        $this->authenticateAsAdmin();
        $proposal = $this->createProposal();

        $response = $this->post(route('admin.proposals.store_note', $proposal->id), [
            'content' => 'Internal note.',
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tblnotes', [
            'rel_id' => $proposal->id,
            'rel_type' => 'proposal',
        ]);
    }

    public function test_admin_can_send_proposal(): void
    {
        $this->authenticateAsAdmin();
        $proposal = $this->createProposal(['status' => 4]);

        $response = $this->post(route('admin.proposals.send', $proposal->id));

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tblproposals', [
            'id' => $proposal->id,
            'status' => 1,
        ]);
    }

    public function test_filter_proposals_by_status(): void
    {
        $this->authenticateAsAdmin();
        $this->createProposal(['subject' => 'Open Proposal', 'status' => 1]);
        $this->createProposal(['subject' => 'Accepted Proposal', 'status' => 2]);

        $response = $this->get(route('admin.proposals.index', ['status' => 2]));
        $response->assertStatus(200);
        $response->assertSee('Accepted Proposal');
        $response->assertDontSee('Open Proposal');
    }
}
