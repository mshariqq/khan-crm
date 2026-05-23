<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffModuleTest extends TestCase
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

    public function test_guest_is_redirected_to_login_when_accessing_staff(): void
    {
        $this->get(route('admin.staff.index'))->assertRedirect(route('login.staff'));
        $this->get(route('admin.staff.create'))->assertRedirect(route('login.staff'));
        $this->post(route('admin.staff.store'), [])->assertRedirect(route('login.staff'));
        $this->get(route('admin.staff.edit', 1))->assertRedirect(route('login.staff'));
        $this->put(route('admin.staff.update', 1), [])->assertRedirect(route('login.staff'));
        $this->delete(route('admin.staff.destroy', 1))->assertRedirect(route('login.staff'));
    }

    public function test_admin_can_view_staff_list(): void
    {
        $this->authenticateAsAdmin();

        $response = $this->get(route('admin.staff.index'));
        $response->assertStatus(200);
        $response->assertSee('Staff Members');
        $response->assertSee('Admin');
        $response->assertSee('admin@crm.com');
    }

    public function test_admin_can_view_create_staff_page(): void
    {
        $this->authenticateAsAdmin();

        $response = $this->get(route('admin.staff.create'));
        $response->assertStatus(200);
        $response->assertSee('Add New Staff Member');
        $response->assertSee('Super Administrator');
    }

    public function test_admin_can_create_staff(): void
    {
        $this->authenticateAsAdmin();

        $response = $this->post(route('admin.staff.store'), [
            'firstname' => 'Test',
            'lastname' => 'Staff',
            'email' => 'teststaff@crm.com',
            'password' => 'secret123',
            'role' => 1,
            'active' => 1,
        ]);

        $response->assertRedirect(route('admin.staff.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tblstaff', [
            'email' => 'teststaff@crm.com',
            'firstname' => 'Test',
            'lastname' => 'Staff',
            'active' => 1,
        ]);

        $listResponse = $this->get(route('admin.staff.index'));
        $listResponse->assertSee('Test Staff');
        $listResponse->assertSee('teststaff@crm.com');
        $listResponse->assertSee('Active');
    }

    public function test_create_staff_requires_required_fields(): void
    {
        $this->authenticateAsAdmin();

        $response = $this->post(route('admin.staff.store'), []);
        $response->assertSessionHasErrors(['email', 'firstname', 'lastname', 'password', 'role', 'active']);
    }

    public function test_create_staff_requires_unique_email(): void
    {
        $this->authenticateAsAdmin();

        $response = $this->post(route('admin.staff.store'), [
            'firstname' => 'Another',
            'lastname' => 'Admin',
            'email' => 'admin@crm.com',
            'password' => 'secret123',
            'role' => 1,
            'active' => 1,
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_admin_can_create_staff_with_departments(): void
    {
        $this->authenticateAsAdmin();

        $dept = Department::create(['name' => 'QA']);

        $response = $this->post(route('admin.staff.store'), [
            'firstname' => 'Dept',
            'lastname' => 'User',
            'email' => 'deptuser@crm.com',
            'password' => 'secret123',
            'role' => 1,
            'active' => 1,
            'departments' => [$dept->departmentid],
        ]);

        $response->assertRedirect(route('admin.staff.index'));
        $response->assertSessionHas('success');

        $staff = Staff::where('email', 'deptuser@crm.com')->first();
        $this->assertTrue($staff->departments->contains($dept->departmentid));
    }

    public function test_admin_can_view_edit_staff_page(): void
    {
        $this->authenticateAsAdmin();

        $response = $this->get(route('admin.staff.edit', $this->admin->staffid));
        $response->assertStatus(200);
        $response->assertSee('Edit Staff Member');
        $response->assertSee('Admin');
        $response->assertSee('admin@crm.com');
    }

    public function test_admin_can_update_staff(): void
    {
        $this->authenticateAsAdmin();

        $response = $this->put(route('admin.staff.update', $this->admin->staffid), [
            'firstname' => 'Updated',
            'lastname' => 'Admin',
            'email' => 'admin@crm.com',
            'role' => 1,
            'active' => 1,
        ]);

        $response->assertRedirect(route('admin.staff.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tblstaff', [
            'staffid' => $this->admin->staffid,
            'firstname' => 'Updated',
        ]);

        $listResponse = $this->get(route('admin.staff.index'));
        $listResponse->assertSee('Updated Admin');
    }

    public function test_admin_can_delete_another_staff(): void
    {
        $this->authenticateAsAdmin();
        $target = Staff::create([
            'firstname' => 'Delete',
            'lastname' => 'Me',
            'email' => 'deleteme@crm.com',
            'password' => bcrypt('password'),
            'role' => 1,
            'active' => 1,
            'datecreated' => now(),
        ]);

        $response = $this->delete(route('admin.staff.destroy', $target->staffid));

        $response->assertRedirect(route('admin.staff.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('tblstaff', ['staffid' => $target->staffid]);
    }

    public function test_admin_cannot_delete_own_account(): void
    {
        $this->authenticateAsAdmin();

        $response = $this->delete(route('admin.staff.destroy', $this->admin->staffid));

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('tblstaff', ['staffid' => $this->admin->staffid]);
    }
}
