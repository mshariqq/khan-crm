<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\ProjectActivity;
use App\Models\ProjectDiscussion;
use App\Models\ProjectDiscussionComment;
use App\Models\Task;
use App\Models\TaskTimer;
use App\Models\TaskChecklistItem;
use App\Models\Staff;
use App\Models\Client;
use App\Models\Contact;
use App\Models\Milestone;
use App\Models\Note;
use App\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ProjectModuleTest extends TestCase
{
    use RefreshDatabase;

    protected Staff $admin;
    protected Staff $staffMember;
    protected Client $client;

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
            'company' => 'Test Client Company',
            'active' => 1,
            'datecreated' => now(),
        ]);

        Contact::create([
            'userid' => $this->client->userid,
            'is_primary' => 1,
            'firstname' => 'Primary',
            'lastname' => 'Contact',
            'email' => 'primary@client.com',
            'phonenumber' => '1234567890',
            'password' => bcrypt('password'),
            'datecreated' => now(),
            'active' => 1,
        ]);
    }

    protected function authenticateAsAdmin(): void
    {
        $this->actingAs($this->admin, 'staff');
    }

    protected function authenticateAsStaff(): void
    {
        $this->actingAs($this->staffMember, 'staff');
    }

    protected function createProject(array $overrides = []): Project
    {
        return Project::create(array_merge([
            'name' => 'Default Project',
            'description' => 'Default description',
            'status' => 1,
            'clientid' => $this->client->userid,
            'billing_type' => 1,
            'start_date' => now()->format('Y-m-d'),
            'deadline' => now()->addDays(30)->format('Y-m-d'),
            'project_created' => now()->format('Y-m-d'),
            'project_cost' => 5000.00,
            'project_rate_per_hour' => 75.00,
            'estimated_hours' => 100,
            'addedfrom' => $this->admin->staffid,
            'progress' => 0,
        ], $overrides));
    }

    public function test_guest_is_redirected_to_login_when_accessing_projects(): void
    {
        $this->get(route('admin.projects.index'))->assertRedirect(route('login.staff'));
        $this->get(route('admin.projects.create'))->assertRedirect(route('login.staff'));
        $this->post(route('admin.projects.store'), [])->assertRedirect(route('login.staff'));
    }

    public function test_admin_can_view_projects_list(): void
    {
        $this->authenticateAsAdmin();
        $this->createProject(['name' => 'Visible Project']);

        $response = $this->get(route('admin.projects.index'));
        $response->assertStatus(200);
        $response->assertSee('Visible Project');
    }

    public function test_admin_can_create_project(): void
    {
        Mail::fake();
        $this->authenticateAsAdmin();

        $projectData = [
            'name' => 'Comprehensive Test Project',
            'description' => 'A full test project description',
            'status' => 1,
            'clientid' => $this->client->userid,
            'billing_type' => 1,
            'start_date' => now()->format('Y-m-d'),
            'deadline' => now()->addDays(30)->format('Y-m-d'),
            'project_cost' => 15000.00,
            'project_rate_per_hour' => 100.00,
            'estimated_hours' => 150,
            'staff_ids' => [$this->admin->staffid, $this->staffMember->staffid],
        ];

        $response = $this->post(route('admin.projects.store'), $projectData);

        $response->assertRedirect(route('admin.projects.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tblprojects', [
            'name' => 'Comprehensive Test Project',
            'clientid' => $this->client->userid,
            'status' => 1,
            'billing_type' => 1,
            'project_cost' => 15000.00,
            'project_rate_per_hour' => 100.00,
            'estimated_hours' => 150,
            'addedfrom' => $this->admin->staffid,
        ]);

        $project = Project::where('name', 'Comprehensive Test Project')->first();

        $this->assertDatabaseHas('tblproject_members', [
            'project_id' => $project->id,
            'staff_id' => $this->admin->staffid,
        ]);
        $this->assertDatabaseHas('tblproject_members', [
            'project_id' => $project->id,
            'staff_id' => $this->staffMember->staffid,
        ]);

        $this->assertDatabaseHas('tblnotifications', [
            'touserid' => $this->admin->staffid,
        ]);
        $this->assertDatabaseHas('tblnotifications', [
            'touserid' => $this->staffMember->staffid,
        ]);
    }

    public function test_admin_can_update_project_basic_fields(): void
    {
        Mail::fake();
        $this->authenticateAsAdmin();
        $project = $this->createProject(['name' => 'Original Name']);
        $project->staff()->sync([$this->admin->staffid]);

        $response = $this->put(route('admin.projects.update', $project->id), [
            'name' => 'Updated Project Name',
            'description' => 'Updated description',
            'status' => 1,
            'clientid' => $this->client->userid,
            'billing_type' => 2,
            'start_date' => now()->format('Y-m-d'),
            'deadline' => now()->addDays(60)->format('Y-m-d'),
            'project_cost' => 20000.00,
            'project_rate_per_hour' => 120.00,
            'estimated_hours' => 200,
        ]);

        $response->assertRedirect(route('admin.projects.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tblprojects', [
            'id' => $project->id,
            'name' => 'Updated Project Name',
            'billing_type' => 2,
            'project_cost' => 20000.00,
            'project_rate_per_hour' => 120.00,
        ]);

        $this->assertDatabaseHas('tblproject_activity', [
            'project_id' => $project->id,
            'staff_id' => $this->admin->staffid,
            'description_key' => 'project_activity_project_updated',
        ]);
    }

    public function test_admin_can_delete_project(): void
    {
        $this->authenticateAsAdmin();
        $project = $this->createProject();

        $response = $this->delete(route('admin.projects.destroy', $project->id));
        $response->assertRedirect(route('admin.projects.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('tblprojects', ['id' => $project->id]);
    }

    public function test_admin_can_create_task_in_project(): void
    {
        Mail::fake();
        $this->authenticateAsAdmin();
        $project = $this->createProject();
        $project->staff()->sync([$this->admin->staffid]);

        $response = $this->post(route('admin.tasks.store'), [
            'name' => 'Project Test Task',
            'description' => 'Task description',
            'priority' => 2,
            'status' => 1,
            'rel_id' => $project->id,
            'rel_type' => 'project',
            'startdate' => now()->format('Y-m-d'),
            'duedate' => now()->addDays(14)->format('Y-m-d'),
            'billable' => 1,
            'hourly_rate' => 50.00,
            'assigned_staff' => [$this->admin->staffid],
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('tbltasks', [
            'name' => 'Project Test Task',
            'rel_id' => $project->id,
            'rel_type' => 'project',
            'status' => 1,
            'priority' => 2,
            'addedfrom' => $this->admin->staffid,
        ]);

        $task = Task::where('name', 'Project Test Task')->first();
        $this->assertNotNull($task);

        $this->assertDatabaseHas('tbltask_assigned', [
            'taskid' => $task->id,
            'staffid' => $this->admin->staffid,
        ]);

        $this->assertDatabaseHas('tblproject_activity', [
            'project_id' => $project->id,
            'staff_id' => $this->admin->staffid,
            'description_key' => 'project_activity_task_created',
        ]);
    }

    public function test_admin_can_update_task_status_via_ajax(): void
    {
        $this->authenticateAsAdmin();
        $project = $this->createProject();
        $task = Task::create([
            'name' => 'Status Change Task',
            'priority' => 1,
            'status' => 1,
            'rel_id' => $project->id,
            'rel_type' => 'project',
            'startdate' => now()->format('Y-m-d'),
            'dateadded' => now(),
            'addedfrom' => $this->admin->staffid,
        ]);

        $response = $this->post(route('admin.tasks.update_status', $task->id), [
            'status' => 'in_progress',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'newStatus' => 'in_progress',
        ]);

        $this->assertDatabaseHas('tbltasks', [
            'id' => $task->id,
            'status' => 2,
        ]);

        $this->assertDatabaseHas('tblproject_activity', [
            'project_id' => $project->id,
            'description_key' => 'project_activity_task_status_updated',
        ]);
    }

    public function test_admin_can_create_milestone(): void
    {
        Mail::fake();
        $this->authenticateAsAdmin();
        $project = $this->createProject();
        $project->staff()->sync([$this->admin->staffid, $this->staffMember->staffid]);

        $response = $this->post(route('admin.milestones.store'), [
            'name' => 'Q1 Milestone',
            'project_id' => $project->id,
            'due_date' => now()->addDays(60)->format('Y-m-d'),
            'description' => 'First quarter milestone',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tblmilestones', [
            'name' => 'Q1 Milestone',
            'project_id' => $project->id,
        ]);

        $this->assertDatabaseHas('tblnotifications', [
            'touserid' => $this->staffMember->staffid,
        ]);
    }

    public function test_admin_can_create_project_discussion(): void
    {
        $this->authenticateAsAdmin();
        $project = $this->createProject();

        $response = $this->post(route('admin.projects.discussions.store', $project->id), [
            'subject' => 'Test Discussion Topic',
            'description' => 'Discussion content here',
            'show_to_customer' => true,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tblprojectdiscussions', [
            'project_id' => $project->id,
            'subject' => 'Test Discussion Topic',
            'staff_id' => $this->admin->staffid,
        ]);

        $this->assertDatabaseHas('tblproject_activity', [
            'project_id' => $project->id,
            'description_key' => 'project_activity_discussion_created',
        ]);
    }

    public function test_admin_can_add_discussion_comment(): void
    {
        $this->authenticateAsAdmin();
        $project = $this->createProject();
        $discussion = ProjectDiscussion::create([
            'project_id' => $project->id,
            'subject' => 'Discussion with Comment',
            'description' => 'Original post',
            'datecreated' => now(),
            'staff_id' => $this->admin->staffid,
        ]);

        $response = $this->post(route('admin.projects.discussions.comment', [$project->id, $discussion->id]), [
            'content' => 'This is a test comment',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJsonPath('comment.content', 'This is a test comment');

        $this->assertDatabaseHas('tblprojectdiscussioncomments', [
            'discussion_id' => $discussion->id,
            'content' => 'This is a test comment',
            'staff_id' => $this->admin->staffid,
        ]);

        $this->assertDatabaseHas('tblproject_activity', [
            'project_id' => $project->id,
            'description_key' => 'project_activity_discussion_commented',
        ]);
    }

    public function test_admin_can_create_project_note(): void
    {
        $this->authenticateAsAdmin();
        $project = $this->createProject();

        $response = $this->post(route('admin.projects.notes.store', $project->id), [
            'description' => 'Important project note',
            'title' => 'Note Title',
            'rel_type' => 'project',
            'rel_id' => $project->id,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('tblnotes', [
            'rel_id' => $project->id,
            'rel_type' => 'project',
            'title' => 'Note Title',
            'description' => 'Important project note',
            'addedfrom' => $this->admin->staffid,
        ]);

        $this->assertDatabaseHas('tblproject_activity', [
            'project_id' => $project->id,
            'description_key' => 'project_activity_note_created',
        ]);
    }

    public function test_admin_can_send_project_notification(): void
    {
        Mail::fake();
        $this->authenticateAsAdmin();
        $project = $this->createProject();
        $project->staff()->sync([$this->admin->staffid]);

        $response = $this->post(route('admin.projects.send_notification', $project->id), [
            'slug' => 'project-updated',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    public function test_project_tab_returns_json_content(): void
    {
        $this->authenticateAsAdmin();
        $project = $this->createProject();

        $response = $this->get(route('admin.projects.tab', [$project->id, 'overview']));
        $response->assertStatus(200);
        $response->assertJsonStructure(['html']);
    }

    public function test_project_update_with_status_change_to_completed_sends_notifications(): void
    {
        Mail::fake();
        $this->authenticateAsAdmin();
        $project = $this->createProject(['status' => 1]);
        $project->staff()->sync([$this->admin->staffid, $this->staffMember->staffid]);

        $response = $this->put(route('admin.projects.update', $project->id), [
            'name' => $project->name,
            'description' => $project->description,
            'status' => 2,
            'clientid' => $this->client->userid,
            'billing_type' => $project->billing_type,
            'start_date' => $project->start_date,
        ]);

        $response->assertRedirect(route('admin.projects.index'));

        $this->assertDatabaseHas('tblprojects', [
            'id' => $project->id,
            'status' => 2,
        ]);

        $this->assertDatabaseHas('tblnotifications', [
            'touserid' => $this->staffMember->staffid,
        ]);
    }

    public function test_admin_can_quick_create_task(): void
    {
        $this->authenticateAsAdmin();
        $project = $this->createProject();

        $response = $this->post(route('admin.tasks.quick_create'), [
            'name' => 'Quick Created Task',
            'priority' => 2,
            'status' => 1,
            'rel_id' => $project->id,
            'rel_type' => 'project',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJsonPath('task.name', 'Quick Created Task');

        $this->assertDatabaseHas('tbltasks', [
            'name' => 'Quick Created Task',
            'rel_id' => $project->id,
            'rel_type' => 'project',
        ]);

        $this->assertDatabaseHas('tblproject_activity', [
            'project_id' => $project->id,
            'description_key' => 'project_activity_task_quick_created',
        ]);
    }

    public function test_project_create_sends_email_notification(): void
    {
        $this->authenticateAsAdmin();
        \Illuminate\Support\Facades\DB::table('tbloptions')->where('name', 'smtp_email')->update(['value' => 'noreply@crm.com']);
        \Illuminate\Support\Facades\Cache::forget('settings_autoload');

        $this->post(route('admin.projects.store'), [
            'name' => 'Email Notification Test',
            'description' => 'Testing email notification',
            'status' => 1,
            'clientid' => $this->client->userid,
            'billing_type' => 1,
            'start_date' => now()->format('Y-m-d'),
            'deadline' => now()->addDays(30)->format('Y-m-d'),
            'project_cost' => 5000.00,
            'project_rate_per_hour' => 100.00,
            'estimated_hours' => 50,
            'staff_ids' => [$this->admin->staffid, $this->staffMember->staffid],
        ]);

        $project = Project::where('name', 'Email Notification Test')->first();

        $this->assertDatabaseHas('tblnotifications', [
            'touserid' => $this->admin->staffid,
        ]);
        $this->assertDatabaseHas('tblnotifications', [
            'touserid' => $this->staffMember->staffid,
        ]);

        $this->assertNotEmpty(Mail::getSymfonyTransport()->messages());
    }

    public function test_project_update_with_new_staff_triggers_in_app_notification(): void
    {
        $this->authenticateAsAdmin();
        $project = $this->createProject();
        $project->staff()->sync([$this->admin->staffid]);

        $this->put(route('admin.projects.update', $project->id), [
            'name' => $project->name,
            'description' => $project->description,
            'status' => 1,
            'clientid' => $this->client->userid,
            'billing_type' => $project->billing_type,
            'start_date' => $project->start_date,
            'deadline' => $project->deadline,
            'staff_ids' => [$this->admin->staffid, $this->staffMember->staffid],
        ]);

        $this->assertDatabaseHas('tblnotifications', [
            'touserid' => $this->staffMember->staffid,
        ]);
    }

    public function test_project_send_notification_endpoint_sends_email(): void
    {
        $this->authenticateAsAdmin();
        \Illuminate\Support\Facades\DB::table('tbloptions')->where('name', 'smtp_email')->update(['value' => 'noreply@crm.com']);
        \Illuminate\Support\Facades\Cache::forget('settings_autoload');
        $project = $this->createProject();
        $project->staff()->sync([$this->admin->staffid]);

        $this->post(route('admin.projects.send_notification', $project->id), [
            'slug' => 'project-updated',
        ]);

        $this->assertNotEmpty(Mail::getSymfonyTransport()->messages());
    }

    public function test_project_kickoff_sends_email(): void
    {
        $this->authenticateAsAdmin();
        \Illuminate\Support\Facades\DB::table('tbloptions')->where('name', 'smtp_email')->update(['value' => 'noreply@crm.com']);
        \Illuminate\Support\Facades\Cache::forget('settings_autoload');
        $project = $this->createProject();
        $project->staff()->sync([$this->admin->staffid]);

        $this->post(route('admin.projects.send', $project->id));

        $this->assertNotEmpty(Mail::getSymfonyTransport()->messages());
    }
}
