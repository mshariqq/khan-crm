<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\ProjectActivity;
use App\Models\ProjectFile;
use App\Models\ProjectNote;
use App\Models\ProjectDiscussion;
use App\Models\ProjectDiscussionComment;
use App\Models\Milestone;
use App\Models\PinnedProject;
use App\Models\Staff;
use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectFactoryTest extends TestCase
{
    use RefreshDatabase;

    protected Staff $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $this->admin = Staff::where('email', 'admin@crm.com')->first();
    }

    public function test_project_factory_creates_project(): void
    {
        Client::factory()->create(['company' => 'Factory Client']);

        $project = Project::factory()->create([
            'clientid' => Client::first()->userid,
            'addedfrom' => $this->admin->staffid,
        ]);

        $this->assertDatabaseHas('tblprojects', ['id' => $project->id]);
        $this->assertNotNull($project->name);
        $this->assertNotNull($project->clientid);
    }

    public function test_project_factory_states(): void
    {
        Client::factory()->create(['company' => 'States Client']);

        $active = Project::factory()->active()->create([
            'clientid' => Client::first()->userid,
            'addedfrom' => $this->admin->staffid,
        ]);
        $this->assertEquals(Project::STATUS_ACTIVE, $active->status);

        $completed = Project::factory()->completed()->create([
            'clientid' => Client::first()->userid,
            'addedfrom' => $this->admin->staffid,
        ]);
        $this->assertEquals(Project::STATUS_COMPLETED, $completed->status);
        $this->assertEquals(100, $completed->progress);

        $cancelled = Project::factory()->cancelled()->create([
            'clientid' => Client::first()->userid,
            'addedfrom' => $this->admin->staffid,
        ]);
        $this->assertEquals(Project::STATUS_CANCELLED, $cancelled->status);
    }

    public function test_milestone_factory_creates_milestone(): void
    {
        Client::factory()->create(['company' => 'Milestone Factory']);
        $project = Project::factory()->create([
            'clientid' => Client::first()->userid,
            'addedfrom' => $this->admin->staffid,
        ]);

        $milestone = Milestone::factory()->create(['project_id' => $project->id]);

        $this->assertDatabaseHas('tblmilestones', ['id' => $milestone->id]);
        $this->assertEquals($project->id, $milestone->project_id);
    }

    public function test_project_note_factory_creates_note(): void
    {
        Client::factory()->create(['company' => 'Note Factory']);
        $project = Project::factory()->create([
            'clientid' => Client::first()->userid,
            'addedfrom' => $this->admin->staffid,
        ]);

        $note = ProjectNote::factory()->create([
            'project_id' => $project->id,
            'staff_id' => $this->admin->staffid,
        ]);

        $this->assertDatabaseHas('tblproject_notes', ['id' => $note->id]);
        $this->assertEquals($project->id, $note->project_id);
    }

    public function test_project_discussion_factory_creates_discussion(): void
    {
        Client::factory()->create(['company' => 'Discussion Factory']);
        $project = Project::factory()->create([
            'clientid' => Client::first()->userid,
            'addedfrom' => $this->admin->staffid,
        ]);

        $discussion = ProjectDiscussion::factory()->create([
            'project_id' => $project->id,
            'staff_id' => $this->admin->staffid,
        ]);

        $this->assertDatabaseHas('tblprojectdiscussions', ['id' => $discussion->id]);
    }

    public function test_project_file_factory_creates_file(): void
    {
        Client::factory()->create(['company' => 'File Factory']);
        $project = Project::factory()->create([
            'clientid' => Client::first()->userid,
            'addedfrom' => $this->admin->staffid,
        ]);

        $file = ProjectFile::factory()->create([
            'project_id' => $project->id,
            'staffid' => $this->admin->staffid,
        ]);

        $this->assertDatabaseHas('tblproject_files', ['id' => $file->id]);
        $this->assertNotNull($file->file_size);
    }

    public function test_project_activity_factory_creates_activity(): void
    {
        Client::factory()->create(['company' => 'Activity Factory']);
        $project = Project::factory()->create([
            'clientid' => Client::first()->userid,
            'addedfrom' => $this->admin->staffid,
        ]);

        $activity = ProjectActivity::factory()->create([
            'project_id' => $project->id,
            'staff_id' => $this->admin->staffid,
        ]);

        $this->assertDatabaseHas('tblproject_activity', ['id' => $activity->id]);
        $this->assertEquals('project_activity_project_created', $activity->description_key);
    }
}
