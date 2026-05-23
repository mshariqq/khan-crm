<?php

namespace Tests\Feature;

use App\Models\CompanyHoliday;
use App\Models\LeaveType;
use App\Models\Staff;
use App\Models\StaffLeave;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaveModuleTest extends TestCase
{
    use RefreshDatabase;

    protected Staff $admin;
    protected LeaveType $leaveType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $this->admin = Staff::where('email', 'admin@crm.com')->first();
        $this->leaveType = LeaveType::create(['name' => 'Annual Leave', 'default_quota' => 20, 'is_paid' => true]);
    }

    protected function authenticateAsAdmin(): void
    {
        $this->actingAs($this->admin, 'staff');
    }

    public function test_guest_is_redirected_to_login_when_accessing_leaves(): void
    {
        $this->get(route('admin.leaves.index'))->assertRedirect(route('login.staff'));
        $this->get(route('admin.leaves.create'))->assertRedirect(route('login.staff'));
        $this->post(route('admin.leaves.store'), [])->assertRedirect(route('login.staff'));
    }

    public function test_admin_can_view_leaves_list(): void
    {
        $this->authenticateAsAdmin();

        $response = $this->get(route('admin.leaves.index'));
        $response->assertStatus(200);
        $response->assertSee('Leave Requests');
    }

    public function test_admin_can_view_create_leave_page(): void
    {
        $this->authenticateAsAdmin();

        $response = $this->get(route('admin.leaves.create'));
        $response->assertStatus(200);
        $response->assertSee('Request Time Off');
        $response->assertSee('Annual Leave');
    }

    public function test_admin_can_submit_leave_request(): void
    {
        $this->authenticateAsAdmin();

        $response = $this->post(route('admin.leaves.store'), [
            'leave_type_id' => $this->leaveType->id,
            'start_date' => now()->addDays(1)->format('Y-m-d'),
            'end_date' => now()->addDays(3)->format('Y-m-d'),
            'reason' => 'Family vacation',
        ]);

        $response->assertRedirect(route('admin.leaves.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tblstaff_leaves', [
            'staffid' => $this->admin->staffid,
            'leave_type_id' => $this->leaveType->id,
            'reason' => 'Family vacation',
            'status' => 'Pending',
        ]);

        $listResponse = $this->get(route('admin.leaves.index'));
        $listResponse->assertSee('Family vacation');
        $listResponse->assertSee('Pending');
    }

    public function test_create_leave_requires_required_fields(): void
    {
        $this->authenticateAsAdmin();

        $response = $this->post(route('admin.leaves.store'), []);
        $response->assertSessionHasErrors(['leave_type_id', 'start_date', 'end_date', 'reason']);
    }

    public function test_admin_can_approve_leave_request(): void
    {
        $this->authenticateAsAdmin();
        $leave = StaffLeave::create([
            'staffid' => $this->admin->staffid,
            'leave_type_id' => $this->leaveType->id,
            'start_date' => now()->addDays(5)->format('Y-m-d'),
            'end_date' => now()->addDays(6)->format('Y-m-d'),
            'reason' => 'Medical appointment',
            'status' => 'Pending',
        ]);

        $response = $this->patch(route('admin.leaves.updateStatus', $leave->id), [
            'status' => 'Approved',
        ]);

        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('tblstaff_leaves', [
            'id' => $leave->id,
            'status' => 'Approved',
            'approved_by' => $this->admin->staffid,
        ]);
    }

    public function test_admin_can_reject_leave_request(): void
    {
        $this->authenticateAsAdmin();
        $leave = StaffLeave::create([
            'staffid' => $this->admin->staffid,
            'leave_type_id' => $this->leaveType->id,
            'start_date' => now()->addDays(10)->format('Y-m-d'),
            'end_date' => now()->addDays(11)->format('Y-m-d'),
            'reason' => 'Personal day',
            'status' => 'Pending',
        ]);

        $response = $this->patch(route('admin.leaves.updateStatus', $leave->id), [
            'status' => 'Rejected',
        ]);

        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('tblstaff_leaves', [
            'id' => $leave->id,
            'status' => 'Rejected',
            'approved_by' => $this->admin->staffid,
        ]);
    }

    public function test_admin_can_view_holidays_page(): void
    {
        $this->authenticateAsAdmin();

        $response = $this->get(route('admin.leaves.holidays'));
        $response->assertStatus(200);
        $response->assertSee('Company Holidays');
    }

    public function test_admin_can_add_holiday(): void
    {
        $this->authenticateAsAdmin();

        $response = $this->post(route('admin.leaves.storeHoliday'), [
            'name' => 'New Year',
            'date' => '2026-01-01',
            'description' => 'New Year celebration',
        ]);

        $response->assertRedirect(route('admin.leaves.holidays'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tblcompany_holidays', [
            'name' => 'New Year',
            'date' => '2026-01-01',
        ]);

        $holidaysResponse = $this->get(route('admin.leaves.holidays'));
        $holidaysResponse->assertSee('New Year');
        $holidaysResponse->assertSee('2026-01-01');
    }

    public function test_admin_can_delete_holiday(): void
    {
        $this->authenticateAsAdmin();
        $holiday = CompanyHoliday::create([
            'name' => 'Christmas',
            'date' => '2026-12-25',
            'description' => 'Christmas holiday',
        ]);

        $response = $this->delete(route('admin.leaves.destroyHoliday', $holiday->id));

        $response->assertRedirect(route('admin.leaves.holidays'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('tblcompany_holidays', ['id' => $holiday->id]);
    }

    public function test_admin_can_filter_leaves_by_status(): void
    {
        $this->authenticateAsAdmin();
        StaffLeave::create([
            'staffid' => $this->admin->staffid,
            'leave_type_id' => $this->leaveType->id,
            'start_date' => now()->addDays(14)->format('Y-m-d'),
            'end_date' => now()->addDays(14)->format('Y-m-d'),
            'reason' => 'Filter test approved',
            'status' => 'Approved',
        ]);
        StaffLeave::create([
            'staffid' => $this->admin->staffid,
            'leave_type_id' => $this->leaveType->id,
            'start_date' => now()->addDays(15)->format('Y-m-d'),
            'end_date' => now()->addDays(15)->format('Y-m-d'),
            'reason' => 'Filter test pending',
            'status' => 'Pending',
        ]);

        $response = $this->get(route('admin.leaves.index', ['status' => 'Approved']));
        $response->assertSee('Filter test approved');
        $response->assertDontSee('Filter test pending');
    }
}
