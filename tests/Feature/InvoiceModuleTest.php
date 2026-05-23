<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Contact;
use App\Models\Invoice;
use App\Models\InvoicePaymentRecord;
use App\Models\Itemable;
use App\Models\Note;
use App\Models\Reminder;
use App\Models\SalesActivity;
use App\Models\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class InvoiceModuleTest extends TestCase
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
            'company' => 'Invoice Test Client',
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
            'firstname' => 'Invoice',
            'lastname' => 'Contact',
            'email' => 'invoice.contact@example.com',
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

    protected function createInvoice(array $overrides = []): Invoice
    {
        return Invoice::create(array_merge([
            'number' => 1,
            'clientid' => $this->client->userid,
            'currency' => 1,
            'date' => now()->format('Y-m-d'),
            'datecreated' => now(),
            'subtotal' => 1000.00,
            'total_tax' => 0.00,
            'total' => 1000.00,
            'adjustment' => 0.00,
            'status' => 1,
            'discount_percent' => 0.00,
            'discount_total' => 0.00,
            'discount_type' => 'percent',
            'include_shipping' => 0,
            'show_quantity_as' => '1',
            'recurring' => 0,
            'cancel_overdue_reminders' => 0,
            'addedfrom' => $this->admin->staffid,
            'hash' => \Illuminate\Support\Str::random(32),
            'sent' => 0,
        ], $overrides));
    }

    public function test_guest_is_redirected_to_login_when_accessing_invoices(): void
    {
        $this->get(route('admin.invoices.index'))->assertRedirect(route('login.staff'));
        $this->get(route('admin.invoices.create'))->assertRedirect(route('login.staff'));
        $this->post(route('admin.invoices.store'), [])->assertRedirect(route('login.staff'));
    }

    public function test_admin_can_view_invoices_list(): void
    {
        $this->authenticateAsAdmin();
        $this->createInvoice(['number' => 100]);
        $invoice = $this->createInvoice(['number' => 101]);

        $response = $this->get(route('admin.invoices.index'));
        $response->assertStatus(200);
        $response->assertSee($invoice->number);
    }

    public function test_admin_can_create_invoice(): void
    {
        Mail::fake();
        $this->authenticateAsAdmin();

        $response = $this->post(route('admin.invoices.store'), [
            'number' => '42',
            'clientid' => $this->client->userid,
            'currency' => 1,
            'date' => now()->format('Y-m-d'),
            'duedate' => now()->addDays(30)->format('Y-m-d'),
            'subtotal' => 1500.00,
            'total_tax' => 95.00,
            'total' => 1595.00,
            'adjustment' => 0.00,
            'status' => 1,
            'discount_percent' => 0,
            'discount_total' => 0,
            'discount_type' => 'percent',
            'include_shipping' => 0,
            'show_quantity_as' => '1',
            'clientnote' => 'Thank you for your business',
            'adminnote' => 'Internal note',
        ]);

        $response->assertRedirect(route('admin.invoices.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tblinvoices', [
            'number' => 42,
            'clientid' => $this->client->userid,
            'total' => 1595.00,
            'status' => 1,
        ]);
    }

    public function test_create_invoice_requires_required_fields(): void
    {
        $this->authenticateAsAdmin();

        $response = $this->post(route('admin.invoices.store'), []);

        $response->assertSessionHasErrors(['number', 'clientid', 'currency', 'date', 'total', 'status']);
    }

    public function test_admin_can_create_invoice_with_items(): void
    {
        Mail::fake();
        $this->authenticateAsAdmin();

        $response = $this->post(route('admin.invoices.store'), [
            'number' => '50',
            'clientid' => $this->client->userid,
            'currency' => 1,
            'date' => now()->format('Y-m-d'),
            'subtotal' => 500.00,
            'total_tax' => 50.00,
            'total' => 550.00,
            'adjustment' => 0.00,
            'status' => 1,
            'discount_percent' => 0,
            'discount_total' => 0,
            'discount_type' => 'percent',
            'include_shipping' => 0,
            'show_quantity_as' => '1',
            'item_description' => ['Consulting Service', 'Software License'],
            'item_qty' => [10, 1],
            'item_rate' => [50.00, 500.00],
            'item_amount' => [500.00, 500.00],
        ]);

        $response->assertRedirect(route('admin.invoices.index'));

        $invoice = Invoice::where('number', 50)->first();

        $this->assertDatabaseHas('tblitemable', [
            'rel_id' => $invoice->id,
            'rel_type' => 'invoice',
            'description' => 'Consulting Service',
            'qty' => 10,
            'rate' => 50.00,
        ]);

        $this->assertDatabaseHas('tblitemable', [
            'rel_id' => $invoice->id,
            'rel_type' => 'invoice',
            'description' => 'Software License',
            'qty' => 1,
            'rate' => 500.00,
        ]);
    }

    public function test_admin_can_view_single_invoice(): void
    {
        $this->authenticateAsAdmin();
        $invoice = $this->createInvoice(['number' => 200]);

        $response = $this->get(route('admin.invoices.show', $invoice->id));
        $response->assertStatus(200);
        $response->assertSee('200');
        $response->assertSee('Invoice Test Client');
        $response->assertSee('1,000.00');
        $response->assertSee('Unpaid');
    }

    public function test_admin_can_update_invoice(): void
    {
        $this->authenticateAsAdmin();
        $invoice = $this->createInvoice(['number' => 300, 'total' => 500.00]);

        $response = $this->put(route('admin.invoices.update', $invoice->id), [
            'number' => '300',
            'clientid' => $this->client->userid,
            'currency' => 1,
            'date' => now()->format('Y-m-d'),
            'duedate' => now()->addDays(15)->format('Y-m-d'),
            'subtotal' => 750.00,
            'total_tax' => 0.00,
            'total' => 750.00,
            'adjustment' => 0.00,
            'status' => 1,
            'discount_percent' => 0,
            'discount_total' => 0,
            'discount_type' => 'percent',
            'include_shipping' => 0,
            'show_quantity_as' => '1',
        ]);

        $response->assertRedirect(route('admin.invoices.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tblinvoices', [
            'id' => $invoice->id,
            'total' => 750.00,
        ]);
    }

    public function test_admin_can_delete_invoice(): void
    {
        $this->authenticateAsAdmin();
        $invoice = $this->createInvoice(['number' => 400]);

        $response = $this->delete(route('admin.invoices.destroy', $invoice->id));

        $response->assertRedirect(route('admin.invoices.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('tblinvoices', ['id' => $invoice->id]);
    }

    public function test_record_payment_transitions_status_to_partial_then_paid(): void
    {
        Mail::fake();
        $this->authenticateAsAdmin();
        $invoice = $this->createInvoice(['number' => 500, 'total' => 1000.00, 'subtotal' => 1000.00, 'status' => 1]);

        $response = $this->post(route('admin.invoices.payments.store', $invoice->id), [
            'amount' => 400.00,
            'paymentmode' => 1,
            'date' => now()->format('Y-m-d'),
            'note' => 'First partial payment',
        ]);

        $response->assertRedirect(route('admin.invoices.show', $invoice->id));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tblinvoicepaymentrecords', [
            'invoiceid' => $invoice->id,
            'amount' => 400.00,
        ]);

        $this->assertDatabaseHas('tblinvoices', [
            'id' => $invoice->id,
            'status' => 2,
        ]);

        $response = $this->post(route('admin.invoices.payments.store', $invoice->id), [
            'amount' => 600.00,
            'paymentmode' => 1,
            'date' => now()->format('Y-m-d'),
            'note' => 'Final payment',
        ]);

        $this->assertDatabaseHas('tblinvoices', [
            'id' => $invoice->id,
            'status' => 3,
        ]);
    }

    public function test_mark_invoice_as_paid(): void
    {
        Mail::fake();
        $this->authenticateAsAdmin();
        $invoice = $this->createInvoice(['number' => 600, 'status' => 1]);

        $response = $this->post(route('admin.invoices.mark_paid', $invoice->id));

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tblinvoices', [
            'id' => $invoice->id,
            'status' => 3,
        ]);
    }

    public function test_mark_invoice_as_cancelled(): void
    {
        Mail::fake();
        $this->authenticateAsAdmin();
        $invoice = $this->createInvoice(['number' => 700, 'status' => 1]);

        $response = $this->post(route('admin.invoices.mark_cancelled', $invoice->id));

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tblinvoices', [
            'id' => $invoice->id,
            'status' => 4,
        ]);
    }

    public function test_send_invoice_to_client(): void
    {
        Mail::fake();
        $this->authenticateAsAdmin();
        $invoice = $this->createInvoice(['number' => 800, 'sent' => 0]);

        $response = $this->post(route('admin.invoices.send', $invoice->id));

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tblinvoices', [
            'id' => $invoice->id,
            'sent' => 1,
        ]);
    }

    public function test_store_note_on_invoice(): void
    {
        $this->authenticateAsAdmin();
        $invoice = $this->createInvoice(['number' => 900]);

        $response = $this->post(route('admin.invoices.notes.store', $invoice->id), [
            'description' => 'This is a test note on the invoice.',
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tblnotes', [
            'rel_id' => $invoice->id,
            'rel_type' => 'invoice',
            'description' => 'This is a test note on the invoice.',
        ]);
    }

    public function test_store_reminder_on_invoice(): void
    {
        Mail::fake();
        $this->authenticateAsAdmin();
        $invoice = $this->createInvoice(['number' => 1000]);

        $response = $this->post(route('admin.invoices.reminders.store', $invoice->id), [
            'date' => now()->addDays(7)->format('Y-m-d'),
            'staff' => $this->admin->staffid,
            'description' => 'Follow up on payment',
            'notify_by_email' => 0,
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tblreminders', [
            'rel_id' => $invoice->id,
            'rel_type' => 'invoice',
            'description' => 'Follow up on payment',
            'staff' => $this->admin->staffid,
        ]);
    }

    public function test_filter_invoices_by_status(): void
    {
        $this->authenticateAsAdmin();
        $this->createInvoice(['number' => 2000, 'status' => 1]);
        $this->createInvoice(['number' => 2001, 'status' => 3]);

        $response = $this->get(route('admin.invoices.index', ['status' => 3]));
        $response->assertStatus(200);
        $response->assertSee('2001');
        $response->assertDontSee('2000');
    }

    public function test_invoice_creation_logs_sales_activity(): void
    {
        Mail::fake();
        $this->authenticateAsAdmin();

        $this->post(route('admin.invoices.store'), [
            'number' => '99',
            'clientid' => $this->client->userid,
            'currency' => 1,
            'date' => now()->format('Y-m-d'),
            'subtotal' => 500.00,
            'total_tax' => 0.00,
            'total' => 500.00,
            'adjustment' => 0.00,
            'status' => 1,
            'discount_percent' => 0,
            'discount_total' => 0,
            'discount_type' => 'percent',
            'include_shipping' => 0,
            'show_quantity_as' => '1',
        ]);

        $invoice = Invoice::where('number', 99)->first();

        $this->assertDatabaseHas('tblsales_activity', [
            'rel_id' => $invoice->id,
            'rel_type' => 'invoice',
        ]);
    }

    public function test_send_invoice_triggers_email_notification_to_client(): void
    {
        $this->authenticateAsAdmin();
        \Illuminate\Support\Facades\DB::table('tbloptions')->where('name', 'smtp_email')->update(['value' => 'noreply@crm.com']);
        \Illuminate\Support\Facades\Cache::forget('settings_autoload');
        $invoice = $this->createInvoice(['number' => 8001, 'sent' => 0]);

        $this->post(route('admin.invoices.send', $invoice->id));

        $this->assertDatabaseHas('tblinvoices', [
            'id' => $invoice->id,
            'sent' => 1,
        ]);

        $this->assertNotEmpty(Mail::getSymfonyTransport()->messages());
    }

    public function test_payment_recording_shows_on_invoice_page(): void
    {
        $this->authenticateAsAdmin();
        $invoice = $this->createInvoice(['number' => 8500, 'total' => 1000.00, 'subtotal' => 1000.00, 'status' => 1]);

        $this->post(route('admin.invoices.payments.store', $invoice->id), [
            'amount' => 400.00,
            'paymentmode' => 1,
            'date' => now()->format('Y-m-d'),
        ]);

        $response = $this->get(route('admin.invoices.show', $invoice->id));
        $response->assertStatus(200);
        $response->assertSee('Partial');
        $response->assertSee('400.00');
    }

    public function test_record_payment_triggers_multiple_email_notifications(): void
    {
        $this->authenticateAsAdmin();
        \Illuminate\Support\Facades\DB::table('tbloptions')->where('name', 'smtp_email')->update(['value' => 'noreply@crm.com']);
        \Illuminate\Support\Facades\Cache::forget('settings_autoload');
        $invoice = $this->createInvoice(['number' => 8002, 'total' => 1000.00, 'subtotal' => 1000.00, 'status' => 1]);

        $this->post(route('admin.invoices.payments.store', $invoice->id), [
            'amount' => 1000.00,
            'paymentmode' => 1,
            'date' => now()->format('Y-m-d'),
        ]);

        $this->assertDatabaseHas('tblinvoices', [
            'id' => $invoice->id,
            'status' => 3,
        ]);

        $this->assertNotEmpty(Mail::getSymfonyTransport()->messages());
    }

    public function test_mark_paid_triggers_email_notification(): void
    {
        $this->authenticateAsAdmin();
        \Illuminate\Support\Facades\DB::table('tbloptions')->where('name', 'smtp_email')->update(['value' => 'noreply@crm.com']);
        \Illuminate\Support\Facades\Cache::forget('settings_autoload');
        $invoice = $this->createInvoice(['number' => 8003, 'status' => 1]);

        $this->post(route('admin.invoices.mark_paid', $invoice->id));

        $this->assertDatabaseHas('tblinvoices', [
            'id' => $invoice->id,
            'status' => 3,
        ]);

        $this->assertNotEmpty(Mail::getSymfonyTransport()->messages());
    }

    public function test_mark_cancelled_triggers_email_notification(): void
    {
        $this->authenticateAsAdmin();
        \Illuminate\Support\Facades\DB::table('tbloptions')->where('name', 'smtp_email')->update(['value' => 'noreply@crm.com']);
        \Illuminate\Support\Facades\Cache::forget('settings_autoload');
        $invoice = $this->createInvoice(['number' => 8004, 'status' => 1]);

        $this->post(route('admin.invoices.mark_cancelled', $invoice->id));

        $this->assertDatabaseHas('tblinvoices', [
            'id' => $invoice->id,
            'status' => 4,
        ]);

        $this->assertNotEmpty(Mail::getSymfonyTransport()->messages());
    }
}
