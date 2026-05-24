<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Staff;
use App\Models\Client;
use App\Models\Contact;
use App\Events\ProjectCompleted;
use App\Events\ProjectStatusChanged;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DebugEventTest extends TestCase
{
    use RefreshDatabase;

    protected Staff $admin;
    protected Client $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DatabaseSeeder::class);

        $this->admin = Staff::where('email', 'admin@crm.com')->first();
        $this->client = Client::create([
            'company' => 'Debug Client',
            'active' => 1,
            'datecreated' => now(),
        ]);
    }

    protected function createProject(): Project
    {
        $project = Project::create([
            'name' => 'Debug Project',
            'status' => 1,
            'clientid' => $this->client->userid,
            'billing_type' => 1,
            'start_date' => now()->format('Y-m-d'),
            'project_created' => now()->format('Y-m-d'),
            'addedfrom' => $this->admin->staffid,
            'progress' => 0,
        ]);
        $project->staff()->sync([$this->admin->staffid]);
        return $project;
    }

    public function test_dispatch_project_completed(): void
    {
        $this->actingAs($this->admin, 'staff');
        $project = $this->createProject();

        ProjectCompleted::dispatch($project);
        $this->assertTrue(true);
    }

    public function test_dispatch_project_status_changed(): void
    {
        $this->actingAs($this->admin, 'staff');
        $project = $this->createProject();

        ProjectStatusChanged::dispatch($project, 1, 4);
        $this->assertTrue(true);
    }

    public function test_update_project_to_completed(): void
    {
        $this->actingAs($this->admin, 'staff');
        $project = $this->createProject();

        $project->update(['status' => 4, 'date_finished' => now()]);
        $project->refresh();
        $this->assertEquals(4, $project->status);
    }
}
