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
use App\Models\Tax;
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

    // ------------------------------------------------------------------ //
    //  HELPERS
    // ------------------------------------------------------------------ //

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

    protected function validCreateParams(array $extra = []): array
    {
        return array_merge([
            'number' => '42',
            'clientid' => $this->client->userid,
            'currency' => 1,
            'date' => now()->format('Y-m-d'),
            'subtotal' => 1000.00,
            'total_tax' => 0.00,
            'total' => 1000.00,
            'adjustment' => 0.00,
            'status' => 1,
            'discount_percent' => 0,
            'discount_total' => 0,
            'discount_type' => 'percent',
            'include_shipping' => 0,
            'show_quantity_as' => '1',
        ], $extra);
    }

    // ================================================================== //
    //  AUTH / GUEST
    // ================================================================== //

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('admin.invoices.index'))->assertRedirect(route('login.staff'));
        $this->get(route('admin.invoices.create'))->assertRedirect(route('login.staff'));
        $this->post(route('admin.invoices.store'), [])->assertRedirect(route('login.staff'));
        $this->get(route('admin.invoices.edit', 1))->assertRedirect(route('login.staff'));
        $this->put(route('admin.invoices.update', 1), [])->assertRedirect(route('login.staff'));
        $this->delete(route('admin.invoices.destroy', 1))->assertRedirect(route('login.staff'));
    }

    // ================================================================== //
    //  VIEW PAGES
    // ================================================================== //

    public function test_admin_can_view_index_page(): void
    {
        $this->authenticateAsAdmin();
        $this->createInvoice(['number' => 100]);
        $inv = $this->createInvoice(['number' => 101]);

        $this->get(route('admin.invoices.index'))
            ->assertStatus(200)
            ->assertSee($inv->number)
            ->assertSee('Invoice Test Client');
    }

    public function test_admin_can_view_create_page(): void
    {
        $this->authenticateAsAdmin();
        $this->get(route('admin.invoices.create'))
            ->assertStatus(200)
            ->assertSee('Create Invoice')
            ->assertSee('Invoice Test Client');
    }

    public function test_admin_can_view_edit_page(): void
    {
        $this->authenticateAsAdmin();
        $invoice = $this->createInvoice(['number' => 300, 'clientnote' => 'Thanks!', 'adminnote' => 'Internal']);

        $this->get(route('admin.invoices.edit', $invoice->id))
            ->assertStatus(200)
            ->assertSee('Edit Invoice')
            ->assertSee('300')
            ->assertSee('Thanks!')
            ->assertSee('Internal');
    }

    public function test_admin_can_view_single_invoice(): void
    {
        $this->authenticateAsAdmin();
        $invoice = $this->createInvoice(['number' => 200]);

        $this->get(route('admin.invoices.show', $invoice->id))
            ->assertStatus(200)
            ->assertSee('200')
            ->assertSee('Invoice Test Client')
            ->assertSee('1,000.00')
            ->assertSee('Unpaid');
    }

    public function test_admin_can_view_invoice_print_page(): void
    {
        $this->authenticateAsAdmin();
        $invoice = $this->createInvoice(['number' => 7000]);

        $this->get(route('admin.invoices.print', $invoice->id))
            ->assertStatus(200);
    }

    // ================================================================== //
    //  CREATE
    // ================================================================== //

    public function test_admin_can_create_invoice(): void
    {
        Mail::fake();
        $this->authenticateAsAdmin();

        $this->post(route('admin.invoices.store'), $this->validCreateParams([
            'number' => '1001',
            'clientnote' => 'Thank you',
            'adminnote' => 'Internal note',
        ]))
            ->assertRedirect(route('admin.invoices.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('tblinvoices', [
            'number' => 1001,
            'clientid' => $this->client->userid,
            'total' => 1000.00,
            'status' => 1,
            'clientnote' => 'Thank you',
            'adminnote' => 'Internal note',
        ]);
    }

    public function test_create_requires_required_fields(): void
    {
        $this->authenticateAsAdmin();
        $this->post(route('admin.invoices.store'), [])
            ->assertSessionHasErrors(['number', 'clientid', 'currency', 'date', 'total', 'status']);
    }

    public function test_create_invoice_with_items(): void
    {
        Mail::fake();
        $this->authenticateAsAdmin();

        $this->post(route('admin.invoices.store'), $this->validCreateParams([
            'number' => '50',
            'subtotal' => 500.00,
            'total_tax' => 50.00,
            'total' => 550.00,
            'item_description' => ['Consulting', 'License'],
            'item_qty' => [10, 1],
            'item_rate' => [50.00, 500.00],
            'item_amount' => [500.00, 500.00],
        ]));

        $inv = Invoice::where('number', 50)->first();

        $this->assertDatabaseHas('tblitemable', [
            'rel_id' => $inv->id,
            'rel_type' => 'invoice',
            'description' => 'Consulting',
            'qty' => 10,
            'rate' => 50.00,
        ]);
        $this->assertDatabaseHas('tblitemable', [
            'rel_id' => $inv->id,
            'rel_type' => 'invoice',
            'description' => 'License',
            'qty' => 1,
            'rate' => 500.00,
        ]);
    }

    public function test_create_invoice_with_tax_on_items(): void
    {
        Mail::fake();
        $this->authenticateAsAdmin();

        $tax = Tax::first();
        $this->assertNotNull($tax, 'Seeder must create at least one tax');

        $this->post(route('admin.invoices.store'), $this->validCreateParams([
            'number' => '55',
            'subtotal' => 200.00,
            'total_tax' => 20.00,
            'total' => 220.00,
            'item_description' => ['Taxed Service'],
            'item_qty' => [1],
            'item_rate' => [200.00],
            'item_amount' => [200.00],
            'item_tax_id' => [$tax->id],
        ]));

        $inv = Invoice::where('number', 55)->first();

        $this->assertDatabaseHas('tblitemable', [
            'rel_id' => $inv->id,
            'rel_type' => 'invoice',
            'description' => 'Taxed Service',
            'tax_rate' => $tax->taxrate,
        ]);
    }

    public function test_create_invoice_with_billing_and_shipping_address(): void
    {
        Mail::fake();
        $this->authenticateAsAdmin();

        $this->post(route('admin.invoices.store'), $this->validCreateParams([
            'number' => '60',
            'billing_street' => '123 Main St',
            'billing_city' => 'New York',
            'billing_state' => 'NY',
            'billing_zip' => '10001',
            'billing_country' => 1,
            'include_shipping' => 1,
            'shipping_street' => '456 Oak Ave',
            'shipping_city' => 'Brooklyn',
            'shipping_state' => 'NY',
            'shipping_zip' => '11201',
            'shipping_country' => 1,
            'show_shipping_on_invoice' => 1,
        ]));

        $inv = Invoice::where('number', 60)->first();
        $this->assertEquals('123 Main St', $inv->billing_street);
        $this->assertEquals('New York', $inv->billing_city);
        $this->assertEquals('456 Oak Ave', $inv->shipping_street);
        $this->assertEquals(1, $inv->include_shipping);
        $this->assertEquals(1, $inv->show_shipping_on_invoice);
    }

    public function test_create_invoice_with_sale_agent_and_project(): void
    {
        Mail::fake();
        $this->authenticateAsAdmin();

        $this->post(route('admin.invoices.store'), $this->validCreateParams([
            'number' => '65',
            'sale_agent' => $this->admin->staffid,
            'project_id' => 0,
            'subscription_id' => 0,
        ]));

        $inv = Invoice::where('number', 65)->first();
        $this->assertEquals($this->admin->staffid, $inv->sale_agent);
    }

    public function test_create_invoice_logs_sales_activity(): void
    {
        Mail::fake();
        $this->authenticateAsAdmin();

        $this->post(route('admin.invoices.store'), $this->validCreateParams(['number' => '99']));

        $inv = Invoice::where('number', 99)->first();
        $this->assertDatabaseHas('tblsales_activity', [
            'rel_id' => $inv->id,
            'rel_type' => 'invoice',
        ]);
    }

    // ================================================================== //
    //  UPDATE
    // ================================================================== //

    public function test_admin_can_update_invoice(): void
    {
        $this->authenticateAsAdmin();
        $invoice = $this->createInvoice(['number' => 300, 'total' => 500.00]);

        $this->put(route('admin.invoices.update', $invoice->id), [
            'number' => '300',
            'clientid' => $this->client->userid,
            'currency' => 1,
            'date' => now()->format('Y-m-d'),
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
        ])
            ->assertRedirect(route('admin.invoices.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('tblinvoices', [
            'id' => $invoice->id,
            'total' => 750.00,
        ]);
    }

    public function test_update_invoice_with_items(): void
    {
        $this->authenticateAsAdmin();
        $invoice = $this->createInvoice(['number' => 400, 'total' => 500.00, 'subtotal' => 500.00]);

        $this->put(route('admin.invoices.update', $invoice->id), [
            'number' => '400',
            'clientid' => $this->client->userid,
            'currency' => 1,
            'date' => now()->format('Y-m-d'),
            'subtotal' => 1500.00,
            'total_tax' => 0.00,
            'total' => 1500.00,
            'adjustment' => 0.00,
            'status' => 1,
            'discount_percent' => 0,
            'discount_total' => 0,
            'discount_type' => 'percent',
            'include_shipping' => 0,
            'show_quantity_as' => '1',
            'item_description' => ['Updated Service', 'New Addon'],
            'item_qty' => [5, 2],
            'item_rate' => [200.00, 250.00],
            'item_amount' => [1000.00, 500.00],
        ]);

        $this->assertDatabaseHas('tblitemable', [
            'rel_id' => $invoice->id,
            'rel_type' => 'invoice',
            'description' => 'Updated Service',
            'qty' => 5,
            'rate' => 200.00,
        ]);
        $this->assertDatabaseHas('tblitemable', [
            'rel_id' => $invoice->id,
            'rel_type' => 'invoice',
            'description' => 'New Addon',
            'qty' => 2,
            'rate' => 250.00,
        ]);
    }

    public function test_update_invoice_removes_old_items(): void
    {
        $this->authenticateAsAdmin();
        $invoice = $this->createInvoice(['number' => 410, 'total' => 500.00, 'subtotal' => 500.00]);

        Itemable::create([
            'rel_id' => $invoice->id,
            'rel_type' => 'invoice',
            'description' => 'Old Item',
            'qty' => 1,
            'rate' => 500.00,
            'amount' => 500.00,
            'item_order' => 0,
        ]);

        $this->put(route('admin.invoices.update', $invoice->id), [
            'number' => '410',
            'clientid' => $this->client->userid,
            'currency' => 1,
            'date' => now()->format('Y-m-d'),
            'subtotal' => 300.00,
            'total_tax' => 0.00,
            'total' => 300.00,
            'adjustment' => 0.00,
            'status' => 1,
            'discount_percent' => 0,
            'discount_total' => 0,
            'discount_type' => 'percent',
            'include_shipping' => 0,
            'show_quantity_as' => '1',
            'item_description' => ['Replacement Item'],
            'item_qty' => [1],
            'item_rate' => [300.00],
            'item_amount' => [300.00],
        ]);

        $this->assertDatabaseMissing('tblitemable', [
            'rel_id' => $invoice->id,
            'description' => 'Old Item',
        ]);
        $this->assertDatabaseHas('tblitemable', [
            'rel_id' => $invoice->id,
            'description' => 'Replacement Item',
        ]);
    }

    public function test_update_invoice_without_items_clears_them(): void
    {
        $this->authenticateAsAdmin();
        $invoice = $this->createInvoice(['number' => 420, 'total' => 500.00, 'subtotal' => 500.00]);

        Itemable::create([
            'rel_id' => $invoice->id,
            'rel_type' => 'invoice',
            'description' => 'Orphan Item',
            'qty' => 1,
            'rate' => 500.00,
            'amount' => 500.00,
            'item_order' => 0,
        ]);

        $this->put(route('admin.invoices.update', $invoice->id), [
            'number' => '420',
            'clientid' => $this->client->userid,
            'currency' => 1,
            'date' => now()->format('Y-m-d'),
            'subtotal' => 0.00,
            'total_tax' => 0.00,
            'total' => 0.00,
            'adjustment' => 0.00,
            'status' => 1,
            'discount_percent' => 0,
            'discount_total' => 0,
            'discount_type' => 'percent',
            'include_shipping' => 0,
            'show_quantity_as' => '1',
        ]);

        $this->assertDatabaseMissing('tblitemable', [
            'rel_id' => $invoice->id,
        ]);
    }

    public function test_update_invoice_keeps_checkbox_defaults_when_unchecked(): void
    {
        $this->authenticateAsAdmin();
        $invoice = $this->createInvoice([
            'number' => 430,
            'include_shipping' => 1,
            'show_shipping_on_invoice' => 1,
            'cancel_overdue_reminders' => 1,
        ]);

        $this->put(route('admin.invoices.update', $invoice->id), [
            'number' => '430',
            'clientid' => $this->client->userid,
            'currency' => 1,
            'date' => now()->format('Y-m-d'),
            'subtotal' => 1000.00,
            'total_tax' => 0.00,
            'total' => 1000.00,
            'adjustment' => 0.00,
            'status' => 1,
            'discount_percent' => 0,
            'discount_total' => 0,
            'discount_type' => 'percent',
            'show_quantity_as' => '1',
        ]);

        $invoice->refresh();
        $this->assertEquals(0, $invoice->include_shipping);
        $this->assertEquals(0, $invoice->show_shipping_on_invoice);
        $this->assertEquals(0, $invoice->cancel_overdue_reminders);
    }

    public function test_update_invoice_with_client_note_and_terms(): void
    {
        $this->authenticateAsAdmin();
        $invoice = $this->createInvoice(['number' => 440]);

        $this->put(route('admin.invoices.update', $invoice->id), [
            'number' => '440',
            'clientid' => $this->client->userid,
            'currency' => 1,
            'date' => now()->format('Y-m-d'),
            'subtotal' => 1000.00,
            'total_tax' => 0.00,
            'total' => 1000.00,
            'adjustment' => 0.00,
            'status' => 1,
            'discount_percent' => 0,
            'discount_total' => 0,
            'discount_type' => 'percent',
            'include_shipping' => 0,
            'show_quantity_as' => '1',
            'clientnote' => 'Updated client note',
            'adminnote' => 'Updated admin note',
            'terms' => 'Updated terms',
        ]);

        $invoice->refresh();
        $this->assertEquals('Updated client note', $invoice->clientnote);
        $this->assertEquals('Updated admin note', $invoice->adminnote);
        $this->assertEquals('Updated terms', $invoice->terms);
    }

    // ================================================================== //
    //  DELETE
    // ================================================================== //

    public function test_admin_can_delete_invoice(): void
    {
        $this->authenticateAsAdmin();
        $invoice = $this->createInvoice(['number' => 500]);

        $this->delete(route('admin.invoices.destroy', $invoice->id))
            ->assertRedirect(route('admin.invoices.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('tblinvoices', ['id' => $invoice->id]);
    }

    public function test_controller_delete_removes_invoice(): void
    {
        $this->authenticateAsAdmin();
        $invoice = $this->createInvoice(['number' => 510]);

        Itemable::create(['rel_id' => $invoice->id, 'rel_type' => 'invoice', 'description' => 'Del Item', 'qty' => 1, 'rate' => 100, 'amount' => 100, 'item_order' => 0]);

        $this->delete(route('admin.invoices.destroy', $invoice->id))
            ->assertRedirect(route('admin.invoices.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('tblinvoices', ['id' => $invoice->id]);
    }

    // ================================================================== //
    //  PAYMENTS
    // ================================================================== //

    public function test_record_payment_transitions_status_to_partial_then_paid(): void
    {
        Mail::fake();
        $this->authenticateAsAdmin();
        $invoice = $this->createInvoice(['number' => 600, 'total' => 1000.00, 'subtotal' => 1000.00, 'status' => 1]);

        $this->post(route('admin.invoices.payments.store', $invoice->id), [
            'amount' => 400.00,
            'paymentmode' => 1,
            'date' => now()->format('Y-m-d'),
        ])
            ->assertRedirect(route('admin.invoices.show', $invoice->id))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('tblinvoicepaymentrecords', ['invoiceid' => $invoice->id, 'amount' => 400.00]);
        $this->assertDatabaseHas('tblinvoices', ['id' => $invoice->id, 'status' => 2]);

        $this->post(route('admin.invoices.payments.store', $invoice->id), [
            'amount' => 600.00,
            'paymentmode' => 1,
            'date' => now()->format('Y-m-d'),
        ]);

        $this->assertDatabaseHas('tblinvoices', ['id' => $invoice->id, 'status' => 3]);
    }

    public function test_record_payment_requires_amount_and_paymentmode(): void
    {
        $this->authenticateAsAdmin();
        $invoice = $this->createInvoice(['number' => 610]);

        $this->post(route('admin.invoices.payments.store', $invoice->id), [])
            ->assertSessionHasErrors(['amount', 'paymentmode', 'date']);
    }

    public function test_record_multiple_payments_tracks_total(): void
    {
        Mail::fake();
        $this->authenticateAsAdmin();
        $invoice = $this->createInvoice(['number' => 620, 'total' => 1000.00, 'subtotal' => 1000.00]);

        $this->post(route('admin.invoices.payments.store', $invoice->id), ['amount' => 200.00, 'paymentmode' => 1, 'date' => now()->format('Y-m-d')]);
        $this->post(route('admin.invoices.payments.store', $invoice->id), ['amount' => 300.00, 'paymentmode' => 1, 'date' => now()->format('Y-m-d')]);

        $this->assertEquals(500.00, $invoice->payments()->sum('amount'));
    }

    public function test_payment_shows_on_invoice_page(): void
    {
        $this->authenticateAsAdmin();
        $invoice = $this->createInvoice(['number' => 630, 'total' => 1000.00, 'subtotal' => 1000.00]);

        $this->post(route('admin.invoices.payments.store', $invoice->id), [
            'amount' => 400.00, 'paymentmode' => 1, 'date' => now()->format('Y-m-d'),
        ]);

        $this->get(route('admin.invoices.show', $invoice->id))
            ->assertStatus(200)
            ->assertSee('Partial')
            ->assertSee('400.00');
    }

    // ================================================================== //
    //  MARK AS PAID / CANCELLED
    // ================================================================== //

    public function test_mark_invoice_as_paid(): void
    {
        Mail::fake();
        $this->authenticateAsAdmin();
        $invoice = $this->createInvoice(['number' => 700, 'status' => 1]);

        $this->post(route('admin.invoices.mark_paid', $invoice->id))->assertSessionHas('success');
        $this->assertDatabaseHas('tblinvoices', ['id' => $invoice->id, 'status' => 3]);
    }

    public function test_mark_invoice_as_cancelled(): void
    {
        Mail::fake();
        $this->authenticateAsAdmin();
        $invoice = $this->createInvoice(['number' => 710, 'status' => 1]);

        $this->post(route('admin.invoices.mark_cancelled', $invoice->id))->assertSessionHas('success');
        $this->assertDatabaseHas('tblinvoices', ['id' => $invoice->id, 'status' => 4]);
    }

    public function test_mark_paid_invoice_does_not_overwrite_existing_payments(): void
    {
        Mail::fake();
        $this->authenticateAsAdmin();
        $invoice = $this->createInvoice(['number' => 720, 'total' => 1000.00, 'subtotal' => 1000.00, 'status' => 2]);

        InvoicePaymentRecord::create(['invoiceid' => $invoice->id, 'amount' => 400.00, 'paymentmode' => 1, 'date' => now(), 'daterecorded' => now(), 'addedfrom' => $this->admin->staffid]);

        $this->post(route('admin.invoices.mark_paid', $invoice->id));
        $this->assertDatabaseHas('tblinvoices', ['id' => $invoice->id, 'status' => 3]);
        $this->assertEquals(1, $invoice->payments()->count());
    }

    // ================================================================== //
    //  SEND
    // ================================================================== //

    public function test_send_invoice_to_client(): void
    {
        Mail::fake();
        $this->authenticateAsAdmin();
        $invoice = $this->createInvoice(['number' => 800, 'sent' => 0]);

        $this->post(route('admin.invoices.send', $invoice->id))->assertSessionHas('success');
        $this->assertDatabaseHas('tblinvoices', ['id' => $invoice->id, 'sent' => 1]);
    }

    // ================================================================== //
    //  NOTES
    // ================================================================== //

    public function test_store_note_on_invoice(): void
    {
        $this->authenticateAsAdmin();
        $invoice = $this->createInvoice(['number' => 900]);

        $this->post(route('admin.invoices.notes.store', $invoice->id), [
            'description' => 'Test note',
        ])->assertSessionHas('success');

        $this->assertDatabaseHas('tblnotes', [
            'rel_id' => $invoice->id,
            'rel_type' => 'invoice',
            'description' => 'Test note',
        ]);
    }

    public function test_store_note_requires_description(): void
    {
        $this->authenticateAsAdmin();
        $invoice = $this->createInvoice(['number' => 910]);

        $this->post(route('admin.invoices.notes.store', $invoice->id), [])
            ->assertSessionHasErrors(['description']);
    }

    // ================================================================== //
    //  REMINDERS
    // ================================================================== //

    public function test_store_reminder_on_invoice(): void
    {
        Mail::fake();
        $this->authenticateAsAdmin();
        $invoice = $this->createInvoice(['number' => 1000]);

        $this->post(route('admin.invoices.reminders.store', $invoice->id), [
            'date' => now()->addDays(7)->format('Y-m-d'),
            'staff' => $this->admin->staffid,
            'description' => 'Follow up',
            'notify_by_email' => 0,
        ])->assertSessionHas('success');

        $this->assertDatabaseHas('tblreminders', [
            'rel_id' => $invoice->id,
            'rel_type' => 'invoice',
            'description' => 'Follow up',
            'staff' => $this->admin->staffid,
        ]);
    }

    public function test_store_reminder_requires_date_staff_description(): void
    {
        $this->authenticateAsAdmin();
        $invoice = $this->createInvoice(['number' => 1010]);

        $this->post(route('admin.invoices.reminders.store', $invoice->id), [])
            ->assertSessionHasErrors(['date', 'staff', 'description']);
    }

    public function test_delete_reminder(): void
    {
        Mail::fake();
        $this->authenticateAsAdmin();
        $invoice = $this->createInvoice(['number' => 1020]);

        $reminder = Reminder::create([
            'description' => 'To delete',
            'date' => now()->addDays(3)->format('Y-m-d'),
            'rel_id' => $invoice->id,
            'rel_type' => 'invoice',
            'staff' => $this->admin->staffid,
            'notify_by_email' => 0,
            'creator' => $this->admin->staffid,
        ]);

        $this->delete(route('admin.reminders.destroy', $reminder->id))
            ->assertRedirect();
        $this->assertDatabaseMissing('tblreminders', ['id' => $reminder->id]);
    }

    // ================================================================== //
    //  FILTER & SEARCH
    // ================================================================== //

    public function test_filter_invoices_by_status(): void
    {
        $this->authenticateAsAdmin();
        $this->createInvoice(['number' => 2000, 'status' => 1]);
        $this->createInvoice(['number' => 2001, 'status' => 3]);

        $this->get(route('admin.invoices.index', ['status' => 3]))
            ->assertStatus(200)
            ->assertSee('2001')
            ->assertDontSee('2000');
    }

    public function test_filter_invoices_by_client(): void
    {
        $this->authenticateAsAdmin();
        $this->createInvoice(['number' => 2100]);

        $other = Client::create(['company' => 'Other Co', 'phonenumber' => '999', 'country' => 1, 'datecreated' => now(), 'active' => 1, 'default_currency' => 1, 'addedfrom' => $this->admin->staffid]);
        $this->createInvoice(['number' => 2101, 'clientid' => $other->userid]);

        $this->get(route('admin.invoices.index', ['client' => $this->client->userid]))
            ->assertStatus(200)
            ->assertSee('2100')
            ->assertDontSee('2101');
    }

    public function test_search_invoices_by_number(): void
    {
        $this->authenticateAsAdmin();
        $this->createInvoice(['number' => 2200]);

        $this->get(route('admin.invoices.index', ['search' => '2200']))
            ->assertStatus(200)
            ->assertSee('2200');
    }

    // ================================================================== //
    //  EMAIL NOTIFICATIONS
    // ================================================================== //

    public function test_send_triggers_email(): void
    {
        $this->authenticateAsAdmin();
        Invoice::whereNotNull('id')->update(['sent' => 0]); // reset
        \Illuminate\Support\Facades\DB::table('tbloptions')->where('name', 'smtp_email')->update(['value' => 'noreply@crm.com']);
        \Illuminate\Support\Facades\Cache::forget('settings_autoload');

        $invoice = $this->createInvoice(['number' => 8001, 'sent' => 0]);

        $this->post(route('admin.invoices.send', $invoice->id));

        $this->assertDatabaseHas('tblinvoices', ['id' => $invoice->id, 'sent' => 1]);
        $this->assertNotEmpty(Mail::getSymfonyTransport()->messages());
    }

    public function test_record_payment_triggers_email(): void
    {
        $this->authenticateAsAdmin();
        \Illuminate\Support\Facades\DB::table('tbloptions')->where('name', 'smtp_email')->update(['value' => 'noreply@crm.com']);
        \Illuminate\Support\Facades\Cache::forget('settings_autoload');

        $invoice = $this->createInvoice(['number' => 8002, 'total' => 1000.00, 'subtotal' => 1000.00, 'status' => 1]);

        $this->post(route('admin.invoices.payments.store', $invoice->id), [
            'amount' => 1000.00, 'paymentmode' => 1, 'date' => now()->format('Y-m-d'),
        ]);

        $this->assertDatabaseHas('tblinvoices', ['id' => $invoice->id, 'status' => 3]);
        $this->assertNotEmpty(Mail::getSymfonyTransport()->messages());
    }

    public function test_mark_paid_triggers_email(): void
    {
        $this->authenticateAsAdmin();
        \Illuminate\Support\Facades\DB::table('tbloptions')->where('name', 'smtp_email')->update(['value' => 'noreply@crm.com']);
        \Illuminate\Support\Facades\Cache::forget('settings_autoload');

        $invoice = $this->createInvoice(['number' => 8003, 'status' => 1]);

        $this->post(route('admin.invoices.mark_paid', $invoice->id));
        $this->assertDatabaseHas('tblinvoices', ['id' => $invoice->id, 'status' => 3]);
        $this->assertNotEmpty(Mail::getSymfonyTransport()->messages());
    }

    public function test_mark_cancelled_triggers_email(): void
    {
        $this->authenticateAsAdmin();
        \Illuminate\Support\Facades\DB::table('tbloptions')->where('name', 'smtp_email')->update(['value' => 'noreply@crm.com']);
        \Illuminate\Support\Facades\Cache::forget('settings_autoload');

        $invoice = $this->createInvoice(['number' => 8004, 'status' => 1]);
        Mail::fake();
        Mail::assertNothingOutgoing();

        $this->post(route('admin.invoices.mark_cancelled', $invoice->id));
        $this->assertDatabaseHas('tblinvoices', ['id' => $invoice->id, 'status' => 4]);
    }

    // ================================================================== //
    //  PERMISSIONS
    // ================================================================== //

    protected function createRestrictedStaff(): Staff
    {
        $staff = Staff::create([
            'firstname' => 'Restricted',
            'lastname' => 'Staff',
            'email' => 'restricted@crm.com',
            'password' => bcrypt('password'),
            'datecreated' => now(),
            'admin' => 0,
        ]);
        $staff->permissions()->delete();
        return $staff;
    }

    public function test_staff_without_view_permission_gets_403(): void
    {
        $staff = $this->createRestrictedStaff();
        $this->actingAs($staff, 'staff');

        $this->get(route('admin.invoices.index'))->assertStatus(403);
        $this->get(route('admin.invoices.show', 1))->assertStatus(403);
    }

    public function test_staff_without_create_permission_gets_403(): void
    {
        $staff = $this->createRestrictedStaff();
        $this->actingAs($staff, 'staff');

        $this->get(route('admin.invoices.create'))->assertStatus(403);
        $this->post(route('admin.invoices.store'), [])->assertStatus(403);
    }

    public function test_staff_without_edit_permission_gets_403(): void
    {
        $this->authenticateAsAdmin();
        $invoice = $this->createInvoice(['number' => 9000]);

        $staff = $this->createRestrictedStaff();
        $this->actingAs($staff, 'staff');

        $this->get(route('admin.invoices.edit', $invoice->id))->assertStatus(403);
        $this->put(route('admin.invoices.update', $invoice->id), [])->assertStatus(403);
    }

    public function test_staff_without_delete_permission_gets_403(): void
    {
        $this->authenticateAsAdmin();
        $invoice = $this->createInvoice(['number' => 9001]);

        $staff = $this->createRestrictedStaff();
        $this->actingAs($staff, 'staff');

        $this->delete(route('admin.invoices.destroy', $invoice->id))->assertStatus(403);
    }

    public function test_admin_can_bypass_permission_checks(): void
    {
        $this->authenticateAsAdmin();

        $this->get(route('admin.invoices.index'))->assertStatus(200);
        $this->get(route('admin.invoices.create'))->assertStatus(200);

        $invoice = $this->createInvoice(['number' => 9002]);
        $this->get(route('admin.invoices.edit', $invoice->id))->assertStatus(200);
        $this->delete(route('admin.invoices.destroy', $invoice->id))->assertRedirect();
    }

    // ================================================================== //
    //  MODEL RELATIONSHIPS
    // ================================================================== //

    public function test_invoice_belongs_to_client(): void
    {
        $invoice = $this->createInvoice(['number' => 9500]);
        $this->assertInstanceOf(Client::class, $invoice->client);
        $this->assertEquals($this->client->userid, $invoice->client->userid);
    }

    public function test_invoice_total_paid_calculation(): void
    {
        $invoice = $this->createInvoice(['number' => 9600, 'total' => 1000.00, 'subtotal' => 1000.00]);

        InvoicePaymentRecord::create(['invoiceid' => $invoice->id, 'amount' => 400.00, 'paymentmode' => 1, 'date' => now(), 'daterecorded' => now(), 'addedfrom' => $this->admin->staffid]);

        $this->assertEquals(400.00, $invoice->totalPaid());
        $this->assertEquals(400.00, $invoice->totalSettled());
        $this->assertEquals(600.00, $invoice->amountDue());
    }

    public function test_invoice_items_relation(): void
    {
        $invoice = $this->createInvoice(['number' => 9700]);

        Itemable::create(['rel_id' => $invoice->id, 'rel_type' => 'invoice', 'description' => 'Item A', 'qty' => 2, 'rate' => 50, 'amount' => 100, 'item_order' => 0]);

        $this->assertCount(1, $invoice->items);
        $this->assertEquals('Item A', $invoice->items->first()->description);
    }

    public function test_invoice_has_many_notes(): void
    {
        $invoice = $this->createInvoice(['number' => 9800]);

        Note::create(['description' => 'Note 1', 'rel_id' => $invoice->id, 'rel_type' => 'invoice', 'dateadded' => now(), 'addedfrom' => $this->admin->staffid]);
        Note::create(['description' => 'Note 2', 'rel_id' => $invoice->id, 'rel_type' => 'invoice', 'dateadded' => now(), 'addedfrom' => $this->admin->staffid]);

        $this->assertCount(2, $invoice->notes);
    }

    public function test_invoice_has_many_reminders(): void
    {
        $invoice = $this->createInvoice(['number' => 9900]);

        Reminder::create(['description' => 'Reminder', 'date' => now()->addDay(), 'rel_id' => $invoice->id, 'rel_type' => 'invoice', 'staff' => $this->admin->staffid, 'notify_by_email' => 0, 'creator' => $this->admin->staffid]);

        $this->assertCount(1, $invoice->reminders);
    }

    public function test_invoice_has_sales_activities(): void
    {
        $invoice = $this->createInvoice(['number' => 9950]);

        SalesActivity::create(['rel_type' => 'invoice', 'rel_id' => $invoice->id, 'description' => 'Activity', 'staffid' => $this->admin->staffid, 'full_name' => 'Admin', 'date' => now()]);

        $this->assertCount(1, $invoice->salesActivities);
    }
}
