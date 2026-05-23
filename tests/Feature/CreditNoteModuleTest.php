<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Contact;
use App\Models\CreditNote;
use App\Models\Currency;
use App\Models\Invoice;
use App\Models\InvoicePaymentRecord;
use App\Models\Itemable;
use App\Models\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreditNoteModuleTest extends TestCase
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
            'company' => 'Credit Note Client',
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
            'firstname' => 'Credit',
            'lastname' => 'Client',
            'email' => 'credit.client@example.com',
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

    protected function createCreditNote(array $overrides = []): CreditNote
    {
        return CreditNote::create(array_merge([
            'clientid' => $this->client->userid,
            'number' => 1,
            'currency' => 1,
            'date' => now()->format('Y-m-d'),
            'datecreated' => now(),
            'subtotal' => 500.00,
            'total' => 500.00,
            'adjustment' => 0.00,
            'status' => 1,
            'addedfrom' => $this->admin->staffid,
            'discount_type' => 'percent',
            'show_quantity_as' => 1,
            'include_shipping' => 0,
            'show_shipping_on_credit_note' => 0,
        ], $overrides));
    }

    protected function createInvoice(array $overrides = []): Invoice
    {
        return Invoice::create(array_merge([
            'number' => 1,
            'clientid' => $this->client->userid,
            'currency' => 1,
            'date' => now()->format('Y-m-d'),
            'datecreated' => now(),
            'subtotal' => 1000.00,
            'total' => 1000.00,
            'status' => 1,
            'addedfrom' => $this->admin->staffid,
            'hash' => \Illuminate\Support\Str::random(32),
            'sent' => 0,
        ], $overrides));
    }

    public function test_guest_is_redirected_to_login_when_accessing_credit_notes(): void
    {
        $this->get(route('admin.creditnotes.index'))->assertRedirect(route('login.staff'));
        $this->get(route('admin.creditnotes.create'))->assertRedirect(route('login.staff'));
        $this->post(route('admin.creditnotes.store'), [])->assertRedirect(route('login.staff'));
    }

    public function test_admin_can_view_credit_notes_list(): void
    {
        $this->authenticateAsAdmin();
        $this->createCreditNote();

        $response = $this->get(route('admin.creditnotes.index'));
        $response->assertStatus(200);
        $response->assertSee('0001');
        $response->assertSee('Credit Note Client');
    }

    public function test_admin_can_create_credit_note(): void
    {
        $this->authenticateAsAdmin();

        $response = $this->post(route('admin.creditnotes.store'), [
            'clientid' => $this->client->userid,
            'number' => 10,
            'currency' => 1,
            'date' => now()->format('Y-m-d'),
            'subtotal' => 750.00,
            'total' => 750.00,
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tblcreditnotes', [
            'clientid' => $this->client->userid,
            'number' => 10,
            'total' => 750.00,
        ]);

        $cn = CreditNote::where('number', 10)->first();
        $response->assertRedirect(route('admin.creditnotes.show', $cn->id));
    }

    public function test_create_credit_note_requires_required_fields(): void
    {
        $this->authenticateAsAdmin();

        $response = $this->post(route('admin.creditnotes.store'), []);
        $response->assertSessionHasErrors(['clientid', 'number', 'currency', 'date', 'subtotal', 'total']);
    }

    public function test_admin_can_view_single_credit_note(): void
    {
        $this->authenticateAsAdmin();
        $cn = $this->createCreditNote();

        $response = $this->get(route('admin.creditnotes.show', $cn->id));
        $response->assertStatus(200);
        $response->assertSee('0001');
        $response->assertSee('Credit Note Client');
        $response->assertSee('Total Amount');
    }

    public function test_admin_can_update_credit_note(): void
    {
        $this->authenticateAsAdmin();
        $cn = $this->createCreditNote();

        $response = $this->put(route('admin.creditnotes.update', $cn->id), [
            'clientid' => $this->client->userid,
            'number' => 2,
            'currency' => 1,
            'date' => now()->format('Y-m-d'),
            'subtotal' => 600.00,
            'total' => 600.00,
        ]);

        $response->assertRedirect(route('admin.creditnotes.show', $cn->id));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tblcreditnotes', [
            'id' => $cn->id,
            'total' => 600.00,
        ]);
    }

    public function test_admin_can_apply_credit_to_invoice(): void
    {
        $this->authenticateAsAdmin();
        $cn = $this->createCreditNote(['total' => 500.00, 'subtotal' => 500.00]);
        $invoice = $this->createInvoice(['total' => 1000.00, 'subtotal' => 1000.00]);

        $response = $this->post(route('admin.creditnotes.apply', $cn->id), [
            'invoice_id' => $invoice->id,
            'amount' => 300.00,
        ]);

        $response->assertRedirect(route('admin.creditnotes.show', $cn->id));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tblcredits', [
            'credit_id' => $cn->id,
            'invoice_id' => $invoice->id,
            'amount' => 300.00,
        ]);
    }

    public function test_applying_full_credit_marks_invoice_as_partially_paid(): void
    {
        $this->authenticateAsAdmin();
        $cn = $this->createCreditNote(['total' => 500.00, 'subtotal' => 500.00]);
        $invoice = $this->createInvoice(['total' => 1000.00, 'subtotal' => 1000.00, 'status' => 1]);

        $this->post(route('admin.creditnotes.apply', $cn->id), [
            'invoice_id' => $invoice->id,
            'amount' => 500.00,
        ]);

        $this->assertDatabaseHas('tblinvoices', [
            'id' => $invoice->id,
            'status' => 2,
        ]);
    }

    public function test_applying_full_credit_to_full_invoice_marks_as_paid(): void
    {
        $this->authenticateAsAdmin();
        $cn = $this->createCreditNote(['total' => 1000.00, 'subtotal' => 1000.00]);
        $invoice = $this->createInvoice(['total' => 1000.00, 'subtotal' => 1000.00, 'status' => 1]);

        $this->post(route('admin.creditnotes.apply', $cn->id), [
            'invoice_id' => $invoice->id,
            'amount' => 1000.00,
        ]);

        $this->assertDatabaseHas('tblinvoices', [
            'id' => $invoice->id,
            'status' => 3,
        ]);
    }

    public function test_applied_credit_shows_on_credit_note_page(): void
    {
        $this->authenticateAsAdmin();
        $cn = $this->createCreditNote(['total' => 500.00, 'subtotal' => 500.00]);
        $invoice = $this->createInvoice(['total' => 1000.00, 'subtotal' => 1000.00, 'status' => 1]);

        $this->post(route('admin.creditnotes.apply', $cn->id), [
            'invoice_id' => $invoice->id,
            'amount' => 300.00,
        ]);

        $response = $this->get(route('admin.creditnotes.show', $cn->id));
        $response->assertStatus(200);
        $response->assertSee('Applied Credits');
        $response->assertSee('300.00');
    }

    public function test_admin_can_delete_credit_note(): void
    {
        $this->authenticateAsAdmin();
        $cn = $this->createCreditNote();

        $response = $this->delete(route('admin.creditnotes.destroy', $cn->id));

        $response->assertRedirect(route('admin.creditnotes.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('tblcreditnotes', ['id' => $cn->id]);
    }

    public function test_deleting_credit_note_reverts_invoice_status(): void
    {
        $this->authenticateAsAdmin();
        $cn = $this->createCreditNote(['total' => 500.00, 'subtotal' => 500.00]);
        $invoice = $this->createInvoice(['total' => 1000.00, 'subtotal' => 1000.00, 'status' => 2]);

        $this->post(route('admin.creditnotes.apply', $cn->id), [
            'invoice_id' => $invoice->id,
            'amount' => 500.00,
        ]);

        $this->assertDatabaseHas('tblinvoices', ['id' => $invoice->id, 'status' => 2]);

        $this->delete(route('admin.creditnotes.destroy', $cn->id));

        $this->assertDatabaseHas('tblinvoices', ['id' => $invoice->id, 'status' => 1]);
    }
}
