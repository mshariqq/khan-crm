<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\ProjectActivity;
use App\Models\Task;
use App\Models\Staff;
use App\Models\Client;
use App\Models\Contact;
use App\Models\Notification;
use App\Models\Milestone;
use App\Models\Invoice;
use App\Services\ProjectService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ProjectServiceTest extends TestCase
{
    use RefreshDatabase;

    protected Staff $admin;
    protected Staff $staffMember;
    protected Client $client;
    protected ProjectService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DatabaseSeeder::class);

        $this->admin = Staff::where('email', 'admin@crm.com')->first();

        $this->staffMember = Staff::create([
            'firstname' => 'Test',
            'lastname' => 'Staff',
            'email' => 'teststaff@crm.com',
            'password' => bcrypt('password'),
            'role' => 2,
            'active' => 1,
            'datecreated' => now(),
            'default_language' => 'english',
            'direction' => 'ltr',
        ]);

        $this->client = Client::create([
            'company' => 'Test Client',
            'active' => 1,
            'datecreated' => now(),
        ]);

        $this->service = app(ProjectService::class);
    }

    protected function authenticate(): void
    {
        $this->actingAs($this->admin, 'staff');
    }

    protected function createProject(array $overrides = []): Project
    {
        $project = Project::create(array_merge([
            'name' => 'Service Test Project',
            'description' => 'Test description',
            'status' => Project::STATUS_ACTIVE,
            'clientid' => $this->client->userid,
            'billing_type' => 1,
            'start_date' => now()->format('Y-m-d'),
            'deadline' => now()->addDays(30)->format('Y-m-d'),
            'project_created' => now()->format('Y-m-d'),
            'project_cost' => 10000.00,
            'project_rate_per_hour' => 100.00,
            'estimated_hours' => 100,
            'addedfrom' => $this->admin->staffid,
            'progress' => 0,
        ], $overrides));

        $project->staff()->sync([$this->admin->staffid]);
        return $project;
    }

    public function test_recalculate_progress_from_tasks(): void
    {
        $this->authenticate();
        $project = $this->createProject(['progress_from_tasks' => true]);

        Task::create([
            'name' => 'Task 1', 'status' => Task::STATUS_COMPLETED,
            'rel_id' => $project->id, 'rel_type' => 'project',
            'startdate' => now()->format('Y-m-d'), 'dateadded' => now(),
            'addedfrom' => $this->admin->staffid, 'priority' => 2,
        ]);
        Task::create([
            'name' => 'Task 2', 'status' => Task::STATUS_PENDING,
            'rel_id' => $project->id, 'rel_type' => 'project',
            'startdate' => now()->format('Y-m-d'), 'dateadded' => now(),
            'addedfrom' => $this->admin->staffid, 'priority' => 2,
        ]);

        $this->service->recalculateProgress($project);

        $project->refresh();
        $this->assertEquals(50, $project->progress);
    }

    public function test_recalculate_progress_no_tasks_returns_zero(): void
    {
        $project = $this->createProject(['progress_from_tasks' => true, 'progress' => 25]);
        $this->service->recalculateProgress($project);
        $project->refresh();
        $this->assertEquals(0, $project->progress);
    }

    public function test_recalculate_progress_completes_project_at_100(): void
    {
        $this->authenticate();
        $project = $this->createProject(['progress_from_tasks' => true, 'status' => Project::STATUS_ACTIVE]);

        Task::create([
            'name' => 'Only Task', 'status' => Task::STATUS_COMPLETED,
            'rel_id' => $project->id, 'rel_type' => 'project',
            'startdate' => now()->format('Y-m-d'), 'dateadded' => now(),
            'addedfrom' => $this->admin->staffid, 'priority' => 2,
        ]);

        $this->service->recalculateProgress($project);

        $project->refresh();
        $this->assertEquals(100, $project->progress);
        $this->assertEquals(Project::STATUS_COMPLETED, $project->status);
        $this->assertNotNull($project->date_finished);
    }

    public function test_handle_milestone_auto_complete_all_tasks_done(): void
    {
        $this->authenticate();
        $project = $this->createProject();
        $milestone = Milestone::create([
            'name' => 'Test Milestone',
            'project_id' => $project->id,
            'due_date' => now()->addDays(30)->format('Y-m-d'),
        ]);

        Task::create([
            'name' => 'Milestone Task', 'milestone' => $milestone->id,
            'status' => Task::STATUS_COMPLETED, 'rel_id' => $project->id, 'rel_type' => 'project',
            'startdate' => now()->format('Y-m-d'), 'dateadded' => now(),
            'addedfrom' => $this->admin->staffid, 'priority' => 2,
        ]);

        $notificationMock = $this->createMock(\App\Services\ProjectNotificationService::class);
        $notificationMock->expects($this->once())
            ->method('send')
            ->with('milestone-completed', $project->id, $this->arrayHasKey('milestone_name'));

        $service = new \App\Services\ProjectService($notificationMock);
        $service->handleMilestoneAutoComplete($project, $milestone->id);
    }

    public function test_handle_milestone_auto_complete_skips_when_tasks_incomplete(): void
    {
        $this->authenticate();
        $project = $this->createProject();
        $milestone = Milestone::create([
            'name' => 'Test Milestone',
            'project_id' => $project->id,
            'due_date' => now()->addDays(30)->format('Y-m-d'),
        ]);

        Task::create([
            'name' => 'Incomplete Task', 'milestone' => $milestone->id,
            'status' => Task::STATUS_PENDING, 'rel_id' => $project->id, 'rel_type' => 'project',
            'startdate' => now()->format('Y-m-d'), 'dateadded' => now(),
            'addedfrom' => $this->admin->staffid, 'priority' => 2,
        ]);

        $notificationMock = $this->createMock(\App\Services\ProjectNotificationService::class);
        $notificationMock->expects($this->never())->method('send');

        $service = new \App\Services\ProjectService($notificationMock);
        $service->handleMilestoneAutoComplete($project, $milestone->id);
    }

    public function test_sync_staff_and_notify_adds_new_members(): void
    {
        $this->authenticate();
        $project = $this->createProject();
        $project->staff()->sync([$this->admin->staffid]);

        $this->service->syncStaffAndNotify($project, [$this->admin->staffid, $this->staffMember->staffid]);

        $this->assertDatabaseHas('tblproject_members', [
            'project_id' => $project->id,
            'staff_id' => $this->staffMember->staffid,
        ]);

        $this->assertDatabaseHas('tblnotifications', [
            'touserid' => $this->staffMember->staffid,
        ]);
    }

    public function test_sync_staff_and_notify_removes_members(): void
    {
        $this->authenticate();
        $project = $this->createProject();
        $project->staff()->sync([$this->admin->staffid, $this->staffMember->staffid]);

        $this->service->syncStaffAndNotify($project, [$this->admin->staffid]);

        $this->assertDatabaseMissing('tblproject_members', [
            'project_id' => $project->id,
            'staff_id' => $this->staffMember->staffid,
        ]);
    }

    public function test_handle_budget_tracking_no_threshold_does_nothing(): void
    {
        $this->authenticate();
        $project = $this->createProject(['project_cost' => 10000]);
        $notificationCountBefore = Notification::count();

        $this->service->handleBudgetTracking($project);

        $this->assertEquals($notificationCountBefore, Notification::count());
    }

    public function test_handle_budget_tracking_threshold_exceeded_returns_slug(): void
    {
        $this->authenticate();
        $project = $this->createProject(['project_cost' => 10000]);

        $invoice = new Invoice();
        $invoice->timestamps = false;
        $invoice->clientid = $this->client->userid;
        $invoice->total = 9000;
        $invoice->subtotal = 9000;
        $invoice->number = 1;
        $invoice->prefix = 'INV-';
        $invoice->currency = 1;
        $invoice->hash = md5(uniqid());
        $invoice->discount_type = 'none';
        $invoice->include_shipping = false;
        $invoice->project_id = $project->id;
        $invoice->date = now();
        $invoice->datecreated = now();
        $invoice->addedfrom = $this->admin->staffid;
        $invoice->save();

        $result = $this->service->handleBudgetTracking($project);

        $this->assertEquals('budget-threshold-reached', $result);
    }

    public function test_activity_description_labels_all_have_values(): void
    {
        $labels = Project::activityDescriptionLabels();

        $expectedKeys = [
            'project_activity_project_created',
            'project_activity_project_updated',
            'project_activity_project_deleted',
            'project_activity_status_updated',
            'project_activity_completed',
            'project_activity_member_added',
            'project_activity_member_removed',
            'project_activity_file_uploaded',
            'project_activity_file_deleted',
            'project_activity_discussion_created',
            'project_activity_discussion_commented',
            'project_activity_milestone_created',
            'project_activity_milestone_completed',
            'project_activity_note_created',
            'project_activity_note_updated',
            'project_activity_task_created',
            'project_activity_task_status_updated',
            'project_activity_task_quick_created',
            'project_activity_baseline_captured',
            'project_activity_phase_created',
            'project_activity_risk_added',
            'project_activity_risk_resolved',
            'project_activity_change_request_submitted',
            'project_activity_change_request_approved',
            'project_activity_lesson_added',
        ];

        $this->assertCount(count($expectedKeys), $labels);

        foreach ($expectedKeys as $key) {
            $this->assertArrayHasKey($key, $labels);
            $this->assertNotEmpty($labels[$key]);
        }
    }

    public function test_log_project_activity_creates_record(): void
    {
        $this->authenticate();
        $project = $this->createProject();

        $project->update(['name' => 'Updated Name']);

        $this->service->logProjectActivity($project);

        $this->assertDatabaseHas('tblproject_activity', [
            'project_id' => $project->id,
            'staff_id' => $this->admin->staffid,
            'description_key' => 'project_activity_project_updated',
        ]);
    }
}
