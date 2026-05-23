<?php

namespace Tests\Feature;

use App\Models\LeaveType;
use App\Models\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaveTypeModuleTest extends TestCase
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

    public function test_guest_is_redirected_to_login_when_accessing_leave_types(): void
    {
        $this->get(route('admin.leave-types.index'))->assertRedirect(route('login.staff'));
        $this->get(route('admin.leave-types.create'))->assertRedirect(route('login.staff'));
        $this->post(route('admin.leave-types.store'), [])->assertRedirect(route('login.staff'));
        $this->get(route('admin.leave-types.edit', 1))->assertRedirect(route('login.staff'));
        $this->put(route('admin.leave-types.update', 1), [])->assertRedirect(route('login.staff'));
        $this->delete(route('admin.leave-types.destroy', 1))->assertRedirect(route('login.staff'));
    }

    public function test_admin_can_view_leave_types_list(): void
    {
        $this->authenticateAsAdmin();
        LeaveType::create(['name' => 'Sick Leave', 'default_quota' => 10, 'is_paid' => true]);

        $response = $this->get(route('admin.leave-types.index'));
        $response->assertStatus(200);
        $response->assertSee('Sick Leave');
    }

    public function test_admin_can_view_create_leave_type_page(): void
    {
        $this->authenticateAsAdmin();

        $response = $this->get(route('admin.leave-types.create'));
        $response->assertStatus(200);
        $response->assertSee('Add New Leave Type');
    }

    public function test_admin_can_create_leave_type(): void
    {
        $this->authenticateAsAdmin();

        $response = $this->post(route('admin.leave-types.store'), [
            'name' => 'Annual Leave',
            'default_quota' => 20,
            'is_paid' => true,
        ]);

        $response->assertRedirect(route('admin.leave-types.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tblleave_types', [
            'name' => 'Annual Leave',
            'default_quota' => 20,
            'is_paid' => 1,
        ]);

        $listResponse = $this->get(route('admin.leave-types.index'));
        $listResponse->assertSee('Annual Leave');
        $listResponse->assertSee('20 days');
        $listResponse->assertSee('Paid');
    }

    public function test_create_leave_type_requires_required_fields(): void
    {
        $this->authenticateAsAdmin();

        $response = $this->post(route('admin.leave-types.store'), []);
        $response->assertSessionHasErrors(['name', 'default_quota', 'is_paid']);
    }

    public function test_admin_can_view_edit_leave_type_page(): void
    {
        $this->authenticateAsAdmin();
        $leaveType = LeaveType::create(['name' => 'Personal Leave', 'default_quota' => 5, 'is_paid' => false]);

        $response = $this->get(route('admin.leave-types.edit', $leaveType->id));
        $response->assertStatus(200);
        $response->assertSee('Personal Leave');
    }

    public function test_admin_can_update_leave_type(): void
    {
        $this->authenticateAsAdmin();
        $leaveType = LeaveType::create(['name' => 'Study Leave', 'default_quota' => 10, 'is_paid' => true]);

        $response = $this->put(route('admin.leave-types.update', $leaveType->id), [
            'name' => 'Study Leave Extended',
            'default_quota' => 15,
            'is_paid' => false,
        ]);

        $response->assertRedirect(route('admin.leave-types.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tblleave_types', [
            'id' => $leaveType->id,
            'name' => 'Study Leave Extended',
            'default_quota' => 15,
            'is_paid' => 0,
        ]);

        $listResponse = $this->get(route('admin.leave-types.index'));
        $listResponse->assertSee('Study Leave Extended');
    }

    public function test_admin_can_delete_leave_type(): void
    {
        $this->authenticateAsAdmin();
        $leaveType = LeaveType::create(['name' => 'Temporary Leave', 'default_quota' => 3, 'is_paid' => false]);

        $response = $this->delete(route('admin.leave-types.destroy', $leaveType->id));

        $response->assertRedirect(route('admin.leave-types.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('tblleave_types', ['id' => $leaveType->id]);
    }
}
