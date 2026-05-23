<?php

namespace Tests\Feature;

use App\Models\Staff;
use App\Models\StaffAttendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceModuleTest extends TestCase
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

    public function test_guest_is_redirected_to_login_when_accessing_attendance(): void
    {
        $this->get(route('admin.attendance.index'))->assertRedirect(route('login.staff'));
        $this->post(route('admin.attendance.clock-in'))->assertRedirect(route('login.staff'));
        $this->post(route('admin.attendance.clock-out'))->assertRedirect(route('login.staff'));
    }

    public function test_admin_can_view_attendance_list(): void
    {
        $this->authenticateAsAdmin();

        $response = $this->get(route('admin.attendance.index'));
        $response->assertStatus(200);
        $response->assertSee('Attendance History');
    }

    public function test_admin_can_clock_in(): void
    {
        $this->authenticateAsAdmin();

        $response = $this->post(route('admin.attendance.clock-in'));

        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('tblstaff_attendance', [
            'staffid' => $this->admin->staffid,
            'date' => now()->format('Y-m-d'),
            'status' => 'Present',
        ]);
    }

    public function test_admin_cannot_clock_in_twice(): void
    {
        $this->authenticateAsAdmin();

        $this->post(route('admin.attendance.clock-in'));
        $response = $this->post(route('admin.attendance.clock-in'));

        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
    }

    public function test_admin_can_clock_out(): void
    {
        $this->authenticateAsAdmin();

        $this->post(route('admin.attendance.clock-in'));
        $response = $this->post(route('admin.attendance.clock-out'));

        $response->assertJson(['success' => true]);

        $attendance = StaffAttendance::where('staffid', $this->admin->staffid)
            ->where('date', now()->format('Y-m-d'))
            ->first();

        $this->assertNotNull($attendance->clock_out);
    }

    public function test_admin_cannot_clock_out_without_clock_in(): void
    {
        $this->authenticateAsAdmin();

        $response = $this->post(route('admin.attendance.clock-out'));

        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
    }

    public function test_admin_cannot_clock_out_twice(): void
    {
        $this->authenticateAsAdmin();

        $this->post(route('admin.attendance.clock-in'));
        $this->post(route('admin.attendance.clock-out'));
        $response = $this->post(route('admin.attendance.clock-out'));

        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
    }

    public function test_attendance_record_appears_in_list(): void
    {
        $this->authenticateAsAdmin();

        $this->post(route('admin.attendance.clock-in'));
        $this->post(route('admin.attendance.clock-out'));

        $response = $this->get(route('admin.attendance.index'));
        $response->assertSee('Admin');
        $response->assertSee(now()->format('Y-m-d'));
        $response->assertSee('Present');
    }

    public function test_admin_can_filter_attendance_by_status(): void
    {
        $this->authenticateAsAdmin();

        StaffAttendance::create([
            'staffid' => $this->admin->staffid,
            'date' => now()->subDays(1)->format('Y-m-d'),
            'clock_in' => '09:00:00',
            'clock_out' => '17:00:00',
            'status' => 'Present',
        ]);
        StaffAttendance::create([
            'staffid' => $this->admin->staffid,
            'date' => now()->subDays(2)->format('Y-m-d'),
            'clock_in' => '10:00:00',
            'clock_out' => '16:00:00',
            'status' => 'Late',
        ]);

        $lateDate = now()->subDays(2)->format('Y-m-d');
        $response = $this->get(route('admin.attendance.index', ['status' => 'Late']));
        $response->assertSee($lateDate);
        $response->assertDontSee(now()->subDays(1)->format('Y-m-d'));
    }
}
