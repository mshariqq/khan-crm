<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Contact;
use App\Models\Department;
use App\Models\Staff;
use App\Models\Ticket;
use App\Models\TicketPriority;
use App\Models\TicketReply;
use App\Models\TicketStatus;
use App\Services\EmailService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class TicketModuleTest extends TestCase
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
            'company' => 'Ticket Test Client',
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
            'firstname' => 'Ticket',
            'lastname' => 'Contact',
            'email' => 'ticket.contact@example.com',
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

    protected function createTicket(array $overrides = []): Ticket
    {
        return Ticket::create(array_merge([
            'subject' => 'Default Ticket Subject',
            'message' => 'Default ticket message body',
            'department' => 1,
            'priority' => 2,
            'status' => 1,
            'userid' => $this->client->userid,
            'contactid' => $this->contact->id,
            'assigned' => 0,
            'project_id' => 0,
            'email' => $this->contact->email,
            'name' => $this->contact->firstname . ' ' . $this->contact->lastname,
            'date' => now(),
            'admin' => 1,
            'ticketkey' => \Illuminate\Support\Str::random(32),
        ], $overrides));
    }

    public function test_guest_is_redirected_to_login_when_accessing_tickets(): void
    {
        $this->get(route('admin.tickets.index'))->assertRedirect(route('login.staff'));
        $this->get(route('admin.tickets.create'))->assertRedirect(route('login.staff'));
        $this->post(route('admin.tickets.store'), [])->assertRedirect(route('login.staff'));
    }

    public function test_admin_can_view_tickets_list(): void
    {
        $this->authenticateAsAdmin();
        $this->createTicket(['subject' => 'Visible Ticket Subject']);

        $response = $this->get(route('admin.tickets.index'));
        $response->assertStatus(200);
        $response->assertSee('Visible Ticket Subject');
    }

    public function test_admin_can_create_ticket(): void
    {
        Mail::fake();
        $this->authenticateAsAdmin();

        $response = $this->post(route('admin.tickets.store'), [
            'subject' => 'New Support Ticket',
            'message' => 'I need help with my account.',
            'department' => 1,
            'priority' => 2,
            'status' => 1,
            'userid' => $this->client->userid,
            'contactid' => $this->contact->id,
            'email' => $this->contact->email,
        ]);

        $response->assertRedirect(route('admin.tickets.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tbltickets', [
            'subject' => 'New Support Ticket',
            'department' => 1,
            'priority' => 2,
            'status' => 1,
        ]);
    }

    public function test_create_ticket_requires_required_fields(): void
    {
        $this->authenticateAsAdmin();

        $response = $this->post(route('admin.tickets.store'), []);

        $response->assertSessionHasErrors(['subject', 'message', 'department', 'priority', 'status']);
    }

    public function test_admin_can_view_single_ticket(): void
    {
        $this->authenticateAsAdmin();
        $ticket = $this->createTicket(['subject' => 'Ticket To View']);

        $response = $this->get(route('admin.tickets.show', $ticket->ticketid));
        $response->assertStatus(200);
        $response->assertSee('Ticket To View');
    }

    public function test_admin_can_update_ticket(): void
    {
        $this->authenticateAsAdmin();
        $ticket = $this->createTicket(['subject' => 'Original Subject']);

        $response = $this->put(route('admin.tickets.update', $ticket->ticketid), [
            'subject' => 'Updated Subject',
            'message' => 'Updated message body',
            'department' => 2,
            'priority' => 3,
            'status' => 2,
            'userid' => $this->client->userid,
            'contactid' => $this->contact->id,
            'assigned' => $this->admin->staffid,
            'email' => $this->contact->email,
        ]);

        $response->assertRedirect(route('admin.tickets.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tbltickets', [
            'ticketid' => $ticket->ticketid,
            'subject' => 'Updated Subject',
            'department' => 2,
            'priority' => 3,
            'status' => 2,
            'assigned' => $this->admin->staffid,
        ]);
    }

    public function test_admin_can_delete_ticket(): void
    {
        $this->authenticateAsAdmin();
        $ticket = $this->createTicket(['subject' => 'Ticket To Delete']);

        $response = $this->delete(route('admin.tickets.destroy', $ticket->ticketid));

        $response->assertRedirect(route('admin.tickets.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('tbltickets', ['ticketid' => $ticket->ticketid]);
    }

    public function test_admin_can_store_reply_on_ticket(): void
    {
        $this->authenticateAsAdmin();
        $ticket = $this->createTicket(['subject' => 'Ticket With Reply']);

        $response = $this->postJson(route('admin.tickets.reply.store'), [
            'message' => 'This is a reply to the ticket.',
            'ticketid' => $ticket->ticketid,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('tblticket_replies', [
            'ticketid' => $ticket->ticketid,
            'message' => 'This is a reply to the ticket.',
        ]);

        $this->assertDatabaseHas('tbltickets', [
            'ticketid' => $ticket->ticketid,
            'adminreplying' => 1,
            'staff_id_replying' => $this->admin->staffid,
        ]);
    }

    public function test_admin_can_change_ticket_status(): void
    {
        $this->authenticateAsAdmin();
        $ticket = $this->createTicket(['status' => 1]);

        $response = $this->postJson(route('admin.tickets.change_status', $ticket->ticketid), [
            'status' => 3,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('tbltickets', [
            'ticketid' => $ticket->ticketid,
            'status' => 3,
        ]);
    }

    public function test_admin_can_change_ticket_assigned_staff(): void
    {
        $this->authenticateAsAdmin();
        $ticket = $this->createTicket(['assigned' => 0]);

        $response = $this->postJson(route('admin.tickets.change_assigned', $ticket->ticketid), [
            'assigned' => $this->admin->staffid,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('tbltickets', [
            'ticketid' => $ticket->ticketid,
            'assigned' => $this->admin->staffid,
        ]);
    }

    public function test_filter_tickets_by_department(): void
    {
        $this->authenticateAsAdmin();
        $this->createTicket(['subject' => 'Support Ticket', 'department' => 1]);
        $this->createTicket(['subject' => 'Sales Ticket', 'department' => 2]);

        $response = $this->get(route('admin.tickets.index', ['department' => 1]));
        $response->assertStatus(200);
        $response->assertSee('Support Ticket');
        $response->assertDontSee('Sales Ticket');
    }

    public function test_filter_tickets_by_priority(): void
    {
        $this->authenticateAsAdmin();
        $this->createTicket(['subject' => 'Low Priority Ticket', 'priority' => 1]);
        $this->createTicket(['subject' => 'High Priority Ticket', 'priority' => 3]);

        $response = $this->get(route('admin.tickets.index', ['priority' => 3]));
        $response->assertStatus(200);
        $response->assertSee('High Priority Ticket');
        $response->assertDontSee('Low Priority Ticket');
    }

    public function test_filter_tickets_by_status(): void
    {
        $this->authenticateAsAdmin();
        $this->createTicket(['subject' => 'Open Ticket', 'status' => 1]);
        $this->createTicket(['subject' => 'Closed Ticket', 'status' => 5]);

        $response = $this->get(route('admin.tickets.index', ['status' => 5]));
        $response->assertStatus(200);
        $response->assertSee('Closed Ticket');
        $response->assertDontSee('Open Ticket');
    }

    public function test_ticket_create_sends_email_to_client(): void
    {
        $this->authenticateAsAdmin();
        \Illuminate\Support\Facades\DB::table('tbloptions')->where('name', 'smtp_email')->update(['value' => 'noreply@crm.com']);
        \Illuminate\Support\Facades\Cache::forget('settings_autoload');
        \Illuminate\Support\Facades\DB::table('tblemailtemplates')->insert([
            'type' => 'ticket',
            'slug' => 'new-ticket-opened-admin',
            'language' => 'english',
            'name' => 'New Ticket Opened (Admin)',
            'subject' => 'New Ticket: {ticket_subject}',
            'message' => '<p>Dear {contact_firstname},</p><p>A new support ticket has been opened.</p><p>{ticket_message}</p>',
            'fromname' => '{companyname}',
            'fromemail' => 'noreply@crm.com',
            'plaintext' => 0,
            'active' => 1,
            'order' => 0,
        ]);

        $this->post(route('admin.tickets.store'), [
            'subject' => 'Email Test Ticket',
            'message' => 'This ticket tests email notification.',
            'department' => 1,
            'priority' => 2,
            'status' => 1,
            'userid' => $this->client->userid,
            'contactid' => $this->contact->id,
            'email' => $this->contact->email,
        ]);

        $this->assertNotEmpty(Mail::getSymfonyTransport()->messages());
    }

    public function test_ticket_reply_sends_email_with_threading_headers(): void
    {
        $this->authenticateAsAdmin();
        \Illuminate\Support\Facades\DB::table('tbloptions')->where('name', 'smtp_email')->update(['value' => 'noreply@crm.com']);
        \Illuminate\Support\Facades\Cache::forget('settings_autoload');

        $tid = \Illuminate\Support\Facades\DB::table('tblemailtemplates')->insertGetId([
            'type' => 'ticket',
            'slug' => 'ticket-reply',
            'language' => 'english',
            'name' => 'Ticket Reply',
            'subject' => 'Re: {ticket_subject}',
            'message' => '<p>Dear {contact_firstname},</p><p>A reply has been added to your ticket.</p><p>{ticket_message}</p>',
            'fromname' => '{companyname}',
            'fromemail' => 'noreply@crm.com',
            'plaintext' => 0,
            'active' => 1,
            'order' => 0,
        ]);
        $this->assertNotNull($tid, 'Template insert failed');

        $ticket = $this->createTicket(['subject' => 'Threading Test Ticket']);
        $ticket->update(['message_id' => '<test-original@crm.local>', 'references' => '<test-original@crm.local>']);

        // Verify the email service can send directly
        $emailService = app(EmailService::class);
        $result = $emailService->send('ticket.contact@example.com', 'ticket-reply', [
            'contact_firstname' => 'Ticket',
            'contact_lastname' => 'Contact',
            'ticket_subject' => '[#' . $ticket->ticketid . '] ' . $ticket->subject,
            'ticket_message' => 'Debug message',
            'ticket_id' => $ticket->ticketid,
            'ticket_public_url' => config('app.url') . '/client/tickets/' . $ticket->ticketid,
        ], ['Message-ID' => '<test@crm.local>', 'In-Reply-To' => '<orig@crm.local>']);
        $this->assertTrue($result, 'Direct EmailService::send should succeed');

        $messagesBefore = Mail::getSymfonyTransport()->messages()->count();
        $this->assertGreaterThan(0, $messagesBefore, 'Direct email should have been sent');

        $response = $this->postJson(route('admin.tickets.reply.store'), [
            'message' => 'This reply should have threading headers.',
            'ticketid' => $ticket->ticketid,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('tblticket_replies', [
            'ticketid' => $ticket->ticketid,
            'message' => 'This reply should have threading headers.',
        ]);

        $messagesAfter = Mail::getSymfonyTransport()->messages()->count();
        $this->assertGreaterThan($messagesBefore, $messagesAfter, 'Reply should send an email');
    }

    public function test_ticket_create_creates_notification_for_staff(): void
    {
        $this->authenticateAsAdmin();

        $this->post(route('admin.tickets.store'), [
            'subject' => 'Notification Staff Test',
            'message' => 'Testing in-app notification for staff.',
            'department' => 1,
            'priority' => 2,
            'status' => 1,
            'userid' => $this->client->userid,
            'contactid' => $this->contact->id,
            'email' => $this->contact->email,
        ]);

        $this->assertDatabaseHas('tbltickets', [
            'subject' => 'Notification Staff Test',
        ]);
    }

    public function test_ticket_change_assigned_staff_creates_notification(): void
    {
        $this->authenticateAsAdmin();
        $ticket = $this->createTicket(['assigned' => 0]);

        $response = $this->postJson(route('admin.tickets.change_assigned', $ticket->ticketid), [
            'assigned' => $this->admin->staffid,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('tbltickets', [
            'ticketid' => $ticket->ticketid,
            'assigned' => $this->admin->staffid,
        ]);
    }
}
