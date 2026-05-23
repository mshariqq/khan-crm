<?php

namespace Tests\Feature;

use App\Models\PayslipItem;
use App\Models\Staff;
use App\Models\StaffPayslip;
use App\Models\StaffSalaryConfig;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PayrollModuleTest extends TestCase
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

    public function test_guest_is_redirected_to_login_when_accessing_payroll(): void
    {
        $this->get(route('admin.payroll.index'))->assertRedirect(route('login.staff'));
        $this->get(route('admin.payroll.salary-config', 1))->assertRedirect(route('login.staff'));
        $this->post(route('admin.payroll.salary-config.update', 1), [])->assertRedirect(route('login.staff'));
    }

    public function test_admin_can_view_payroll_list(): void
    {
        $this->authenticateAsAdmin();

        $response = $this->get(route('admin.payroll.index'));
        $response->assertStatus(200);
        $response->assertSee('Monthly Payslips');
    }

    public function test_admin_can_view_salary_config_page(): void
    {
        $this->authenticateAsAdmin();

        $response = $this->get(route('admin.payroll.salary-config', $this->admin->staffid));
        $response->assertStatus(200);
        $response->assertSee('Salary Configuration');
        $response->assertSee($this->admin->fullname);
    }

    public function test_admin_can_update_salary_config(): void
    {
        $this->authenticateAsAdmin();

        $response = $this->post(route('admin.payroll.salary-config.update', $this->admin->staffid), [
            'base_salary' => 5000.00,
            'fixed_allowances' => 500.00,
            'fixed_deductions' => 200.00,
            'effective_date' => '2026-01-01',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tblstaff_salary_config', [
            'staffid' => $this->admin->staffid,
            'base_salary' => 5000.00,
            'fixed_allowances' => 500.00,
            'fixed_deductions' => 200.00,
            'effective_date' => '2026-01-01',
        ]);
    }

    public function test_admin_can_generate_payslips(): void
    {
        $this->authenticateAsAdmin();

        StaffSalaryConfig::create([
            'staffid' => $this->admin->staffid,
            'base_salary' => 5000.00,
            'fixed_allowances' => 500.00,
            'fixed_deductions' => 200.00,
            'effective_date' => '2026-01-01',
        ]);

        $response = $this->post(route('admin.payroll.generate'), [
            'month' => 5,
            'year' => 2026,
        ]);

        $response->assertRedirect(route('admin.payroll.index', ['month' => 5, 'year' => 2026]));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tblstaff_payslips', [
            'staffid' => $this->admin->staffid,
            'month' => 5,
            'year' => 2026,
            'basic' => 5000.00,
            'total_additions' => 500.00,
            'total_deductions' => 200.00,
            'net_amount' => 5300.00,
            'status' => 'Draft',
        ]);

        $this->assertDatabaseHas('tblpayslip_items', [
            'type' => 'Addition',
            'label' => 'Base Salary',
            'amount' => 5000.00,
        ]);

        $this->assertDatabaseHas('tblpayslip_items', [
            'type' => 'Addition',
            'label' => 'Fixed Allowances',
            'amount' => 500.00,
        ]);

        $this->assertDatabaseHas('tblpayslip_items', [
            'type' => 'Deduction',
            'label' => 'Fixed Deductions',
            'amount' => 200.00,
        ]);
    }

    public function test_generated_payslips_appear_in_list_with_view_assertions(): void
    {
        $this->authenticateAsAdmin();

        StaffSalaryConfig::create([
            'staffid' => $this->admin->staffid,
            'base_salary' => 5000.00,
            'fixed_allowances' => 500.00,
            'fixed_deductions' => 200.00,
            'effective_date' => '2026-01-01',
        ]);

        $this->post(route('admin.payroll.generate'), ['month' => 5, 'year' => 2026]);

        $response = $this->get(route('admin.payroll.index', ['month' => 5, 'year' => 2026]));
        $response->assertSee('Admin');
        $response->assertSee('$5,300.00');
        $response->assertSee('Draft');
    }

    public function test_admin_can_view_payslip_detail(): void
    {
        $this->authenticateAsAdmin();

        StaffSalaryConfig::create([
            'staffid' => $this->admin->staffid,
            'base_salary' => 5000.00,
            'fixed_allowances' => 500.00,
            'fixed_deductions' => 200.00,
            'effective_date' => '2026-01-01',
        ]);

        $this->post(route('admin.payroll.generate'), ['month' => 5, 'year' => 2026]);

        $payslip = StaffPayslip::where('staffid', $this->admin->staffid)->first();

        $response = $this->get(route('admin.payroll.view', $payslip->id));
        $response->assertStatus(200);
        $response->assertSee('Payslip');
        $response->assertSee($this->admin->fullname);
        $response->assertSee('$5,300.00');
        $response->assertSee('Base Salary');
        $response->assertSee('Draft');
    }

    public function test_admin_can_add_adjustment_to_payslip(): void
    {
        $this->authenticateAsAdmin();

        StaffSalaryConfig::create([
            'staffid' => $this->admin->staffid,
            'base_salary' => 5000.00,
            'fixed_allowances' => 0,
            'fixed_deductions' => 0,
            'effective_date' => '2026-01-01',
        ]);

        $this->post(route('admin.payroll.generate'), ['month' => 5, 'year' => 2026]);

        $payslip = StaffPayslip::where('staffid', $this->admin->staffid)->first();

        $response = $this->post(route('admin.payroll.adjustment'), [
            'payslip_id' => $payslip->id,
            'type' => 'Addition',
            'label' => 'Performance Bonus',
            'amount' => 1000.00,
        ]);

        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('tblpayslip_items', [
            'payslip_id' => $payslip->id,
            'type' => 'Addition',
            'label' => 'Performance Bonus',
            'amount' => 1000.00,
        ]);

        $payslip->refresh();
        $this->assertEquals(11000.00, $payslip->net_amount);
    }

    public function test_admin_can_mark_payslip_as_paid(): void
    {
        $this->authenticateAsAdmin();

        StaffSalaryConfig::create([
            'staffid' => $this->admin->staffid,
            'base_salary' => 5000.00,
            'fixed_allowances' => 0,
            'fixed_deductions' => 0,
            'effective_date' => '2026-01-01',
        ]);

        $this->post(route('admin.payroll.generate'), ['month' => 5, 'year' => 2026]);

        $payslip = StaffPayslip::where('staffid', $this->admin->staffid)->first();

        $response = $this->post(route('admin.payroll.mark-paid', $payslip->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tblstaff_payslips', [
            'id' => $payslip->id,
            'status' => 'Paid',
        ]);

        $this->assertNotNull($payslip->fresh()->payment_date);
    }

    public function test_paid_payslip_shows_paid_in_list(): void
    {
        $this->authenticateAsAdmin();

        StaffSalaryConfig::create([
            'staffid' => $this->admin->staffid,
            'base_salary' => 5000.00,
            'fixed_allowances' => 0,
            'fixed_deductions' => 0,
            'effective_date' => '2026-01-01',
        ]);

        $this->post(route('admin.payroll.generate'), ['month' => 5, 'year' => 2026]);
        $payslip = StaffPayslip::where('staffid', $this->admin->staffid)->first();
        $this->post(route('admin.payroll.mark-paid', $payslip->id));

        $response = $this->get(route('admin.payroll.index', ['month' => 5, 'year' => 2026]));
        $response->assertSee('Paid');
    }

    public function test_admin_can_filter_payslips_by_status(): void
    {
        $this->authenticateAsAdmin();

        StaffSalaryConfig::create([
            'staffid' => $this->admin->staffid,
            'base_salary' => 5000.00,
            'fixed_allowances' => 0,
            'fixed_deductions' => 0,
            'effective_date' => '2026-01-01',
        ]);

        $this->post(route('admin.payroll.generate'), ['month' => 5, 'year' => 2026]);
        StaffSalaryConfig::create([
            'staffid' => Staff::where('email', 'john@crm.com')->first()->staffid,
            'base_salary' => 4000.00,
            'fixed_allowances' => 0,
            'fixed_deductions' => 0,
            'effective_date' => '2026-01-01',
        ]);
        $this->post(route('admin.payroll.generate'), ['month' => 5, 'year' => 2026]);

        $payslip = StaffPayslip::where('staffid', $this->admin->staffid)->first();
        $this->post(route('admin.payroll.mark-paid', $payslip->id));

        $response = $this->get(route('admin.payroll.index', ['month' => 5, 'year' => 2026, 'status' => 'Paid']));
        $response->assertSee('Paid');
        $response->assertSee('$5,000.00');
    }

    public function test_admin_can_view_my_payslips(): void
    {
        $this->authenticateAsAdmin();

        StaffSalaryConfig::create([
            'staffid' => $this->admin->staffid,
            'base_salary' => 5000.00,
            'fixed_allowances' => 0,
            'fixed_deductions' => 0,
            'effective_date' => '2026-01-01',
        ]);

        $this->post(route('admin.payroll.generate'), ['month' => 5, 'year' => 2026]);

        $response = $this->get(route('admin.payroll.my-payslips'));
        $response->assertStatus(200);
        $response->assertSee('My Payslips');
        $response->assertSee('$5,000.00');
    }
}
