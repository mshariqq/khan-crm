<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DepartmentModuleTest extends TestCase
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

    public function test_guest_is_redirected_to_login_when_accessing_departments(): void
    {
        $this->get(route('admin.departments.index'))->assertRedirect(route('login.staff'));
        $this->get(route('admin.departments.create'))->assertRedirect(route('login.staff'));
        $this->post(route('admin.departments.store'), [])->assertRedirect(route('login.staff'));
        $this->get(route('admin.departments.edit', 1))->assertRedirect(route('login.staff'));
        $this->put(route('admin.departments.update', 1), [])->assertRedirect(route('login.staff'));
        $this->delete(route('admin.departments.destroy', 1))->assertRedirect(route('login.staff'));
    }

    public function test_admin_can_view_departments_list(): void
    {
        $this->authenticateAsAdmin();
        Department::create(['name' => 'Engineering', 'email' => 'eng@crm.com']);

        $response = $this->get(route('admin.departments.index'));
        $response->assertStatus(200);
        $response->assertSee('Engineering');
    }

    public function test_admin_can_view_create_department_page(): void
    {
        $this->authenticateAsAdmin();

        $response = $this->get(route('admin.departments.create'));
        $response->assertStatus(200);
        $response->assertSee('Add New Department');
    }

    public function test_admin_can_create_department(): void
    {
        $this->authenticateAsAdmin();

        $response = $this->post(route('admin.departments.store'), [
            'name' => 'Human Resources',
            'email' => 'hr@crm.com',
            'hidefromclient' => true,
        ]);

        $response->assertRedirect(route('admin.departments.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tbldepartments', [
            'name' => 'Human Resources',
            'email' => 'hr@crm.com',
            'hidefromclient' => 1,
        ]);

        $listResponse = $this->get(route('admin.departments.index'));
        $listResponse->assertSee('Human Resources');
        $listResponse->assertSee('hr@crm.com');
        $listResponse->assertSee('Hidden');
    }

    public function test_create_department_requires_name(): void
    {
        $this->authenticateAsAdmin();

        $response = $this->post(route('admin.departments.store'), []);
        $response->assertSessionHasErrors(['name']);
    }

    public function test_admin_can_view_edit_department_page(): void
    {
        $this->authenticateAsAdmin();
        $department = Department::create(['name' => 'Marketing', 'email' => 'mkt@crm.com']);

        $response = $this->get(route('admin.departments.edit', $department->departmentid));
        $response->assertStatus(200);
        $response->assertSee('Marketing');
    }

    public function test_admin_can_update_department(): void
    {
        $this->authenticateAsAdmin();
        $department = Department::create(['name' => 'R&D', 'email' => 'rd@crm.com']);

        $response = $this->put(route('admin.departments.update', $department->departmentid), [
            'name' => 'Research & Development',
            'email' => 'research@crm.com',
            'hidefromclient' => true,
        ]);

        $response->assertRedirect(route('admin.departments.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tbldepartments', [
            'departmentid' => $department->departmentid,
            'name' => 'Research & Development',
            'email' => 'research@crm.com',
            'hidefromclient' => 1,
        ]);

        $listResponse = $this->get(route('admin.departments.index'));
        $listResponse->assertSee('Research & Development');
    }

    public function test_admin_can_delete_department(): void
    {
        $this->authenticateAsAdmin();
        $department = Department::create(['name' => 'Temp Dept']);

        $response = $this->delete(route('admin.departments.destroy', $department->departmentid));

        $response->assertRedirect(route('admin.departments.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('tbldepartments', ['departmentid' => $department->departmentid]);
    }
}
