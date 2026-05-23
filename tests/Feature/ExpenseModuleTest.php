<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseModuleTest extends TestCase
{
    use RefreshDatabase;

    protected Staff $admin;
    protected Client $client;
    protected ExpenseCategory $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\DatabaseSeeder::class);

        $this->admin = Staff::where('email', 'admin@crm.com')->first();

        $this->client = Client::create([
            'company' => 'Expense Client',
            'phonenumber' => '1234567890',
            'country' => 1,
            'datecreated' => now(),
            'active' => 1,
            'default_currency' => 1,
            'addedfrom' => $this->admin->staffid,
        ]);

        $this->category = ExpenseCategory::create(['name' => 'Office Supplies']);
    }

    protected function authenticateAsAdmin(): void
    {
        $this->actingAs($this->admin, 'staff');
    }

    protected function createExpense(array $overrides = []): Expense
    {
        return Expense::create(array_merge([
            'expense_name' => 'Printer Paper',
            'amount' => 50.00,
            'date' => now()->format('Y-m-d'),
            'clientid' => $this->client->userid,
            'currency' => 1,
            'category' => $this->category->id,
            'project_id' => 0,
            'billable' => 0,
            'recurring' => 0,
            'custom_recurring' => 0,
            'create_invoice_billable' => 0,
            'send_invoice_to_customer' => 0,
            'dateadded' => now(),
            'addedfrom' => $this->admin->staffid,
        ], $overrides));
    }

    public function test_guest_is_redirected_to_login_when_accessing_expenses(): void
    {
        $this->get(route('admin.expenses.index'))->assertRedirect(route('login.staff'));
        $this->get(route('admin.expenses.create'))->assertRedirect(route('login.staff'));
        $this->post(route('admin.expenses.store'), [])->assertRedirect(route('login.staff'));
    }

    public function test_admin_can_view_expenses_list(): void
    {
        $this->authenticateAsAdmin();
        $this->createExpense(['expense_name' => 'Office Chair']);

        $response = $this->get(route('admin.expenses.index'));
        $response->assertStatus(200);
        $response->assertSee('Office Chair');
    }

    public function test_admin_can_create_expense(): void
    {
        $this->authenticateAsAdmin();

        $response = $this->post(route('admin.expenses.store'), [
            'expense_name' => 'Software License',
            'amount' => 299.99,
            'date' => now()->format('Y-m-d'),
            'clientid' => $this->client->userid,
            'currency' => 1,
            'category' => $this->category->id,
            'project_id' => 0,
            'reference_no' => 'INV-2024-001',
            'note' => 'Annual subscription',
        ]);

        $response->assertRedirect(route('admin.expenses.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tblexpenses', [
            'expense_name' => 'Software License',
            'amount' => 299.99,
            'clientid' => $this->client->userid,
            'reference_no' => 'INV-2024-001',
        ]);
    }

    public function test_create_expense_requires_required_fields(): void
    {
        $this->authenticateAsAdmin();

        $response = $this->post(route('admin.expenses.store'), []);
        $response->assertSessionHasErrors(['expense_name', 'amount', 'date', 'clientid', 'currency']);
    }

    public function test_admin_can_view_single_expense(): void
    {
        $this->authenticateAsAdmin();
        $expense = $this->createExpense();

        $response = $this->get(route('admin.expenses.show', $expense->id));
        $response->assertStatus(200);
        $response->assertSee('Printer Paper');
    }

    public function test_admin_can_update_expense(): void
    {
        $this->authenticateAsAdmin();
        $expense = $this->createExpense();

        $response = $this->put(route('admin.expenses.update', $expense->id), [
            'expense_name' => 'Updated Expense',
            'amount' => 75.00,
            'date' => now()->format('Y-m-d'),
            'clientid' => $this->client->userid,
            'currency' => 1,
            'category' => $this->category->id,
        ]);

        $response->assertRedirect(route('admin.expenses.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tblexpenses', [
            'id' => $expense->id,
            'expense_name' => 'Updated Expense',
            'amount' => 75.00,
        ]);
    }

    public function test_admin_can_delete_expense(): void
    {
        $this->authenticateAsAdmin();
        $expense = $this->createExpense();

        $response = $this->delete(route('admin.expenses.destroy', $expense->id));

        $response->assertRedirect(route('admin.expenses.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('tblexpenses', ['id' => $expense->id]);
    }

    public function test_filter_expenses_by_client(): void
    {
        $this->authenticateAsAdmin();
        $client2 = Client::create([
            'company' => 'Second Client',
            'phonenumber' => '5550001111',
            'country' => 1,
            'datecreated' => now(),
            'active' => 1,
            'default_currency' => 1,
            'addedfrom' => $this->admin->staffid,
        ]);

        $this->createExpense(['expense_name' => 'Client 1 Expense']);
        Expense::create([
            'expense_name' => 'Client 2 Expense',
            'amount' => 100.00,
            'date' => now()->format('Y-m-d'),
            'clientid' => $client2->userid,
            'currency' => 1,
            'project_id' => 0,
            'billable' => 0,
            'recurring' => 0,
            'custom_recurring' => 0,
            'create_invoice_billable' => 0,
            'send_invoice_to_customer' => 0,
            'dateadded' => now(),
            'addedfrom' => $this->admin->staffid,
        ]);

        $response = $this->get(route('admin.expenses.index', ['client' => $client2->userid]));
        $response->assertStatus(200);
        $response->assertSee('Client 2 Expense');
        $response->assertDontSee('Client 1 Expense');
    }

    // --- Expense Category Tests ---

    public function test_guest_is_redirected_to_login_when_accessing_expense_categories(): void
    {
        $this->get(route('admin.expense-categories.index'))->assertRedirect(route('login.staff'));
        $this->get(route('admin.expense-categories.create'))->assertRedirect(route('login.staff'));
        $this->post(route('admin.expense-categories.store'), [])->assertRedirect(route('login.staff'));
    }

    public function test_admin_can_view_expense_categories_list(): void
    {
        $this->authenticateAsAdmin();
        ExpenseCategory::create(['name' => 'Travel']);

        $response = $this->get(route('admin.expense-categories.index'));
        $response->assertStatus(200);
        $response->assertSee('Travel');
    }

    public function test_admin_can_create_expense_category(): void
    {
        $this->authenticateAsAdmin();

        $response = $this->post(route('admin.expense-categories.store'), [
            'name' => 'Utilities',
        ]);

        $response->assertRedirect(route('admin.expense-categories.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('expense_categories', [
            'name' => 'Utilities',
        ]);
    }

    public function test_admin_can_update_expense_category(): void
    {
        $this->authenticateAsAdmin();
        $cat = ExpenseCategory::create(['name' => 'Old Name']);

        $response = $this->put(route('admin.expense-categories.update', $cat->id), [
            'name' => 'New Name',
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('expense_categories', [
            'id' => $cat->id,
            'name' => 'New Name',
        ]);
    }

    public function test_admin_can_delete_expense_category(): void
    {
        $this->authenticateAsAdmin();
        $cat = ExpenseCategory::create(['name' => 'Temp']);

        $response = $this->delete(route('admin.expense-categories.destroy', $cat->id));

        $response->assertRedirect(route('admin.expense-categories.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('expense_categories', ['id' => $cat->id]);
    }
}
