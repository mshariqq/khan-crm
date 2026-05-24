<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Staff;
use App\Models\Client;
use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectExportTest extends TestCase
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
            'company' => 'Export Client',
            'active' => 1,
            'datecreated' => now(),
        ]);
    }

    protected function authenticate(): void
    {
        $this->actingAs($this->admin, 'staff');
    }

    protected function createProject(array $overrides = []): Project
    {
        return Project::create(array_merge([
            'name' => 'Export Project',
            'status' => 1,
            'clientid' => $this->client->userid,
            'billing_type' => 1,
            'start_date' => now()->format('Y-m-d'),
            'deadline' => now()->addDays(30)->format('Y-m-d'),
            'project_created' => now()->format('Y-m-d'),
            'project_cost' => 5000,
            'addedfrom' => $this->admin->staffid,
            'progress' => 50,
        ], $overrides));
    }

    public function test_admin_can_export_projects_csv(): void
    {
        $this->authenticate();
        $this->createProject(['name' => 'CSV Export Test']);

        $response = $this->get(route('admin.projects.export'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=utf-8');
        $this->assertStringContainsString('attachment; filename="projects_export_', $response->headers->get('Content-Disposition'));

        $content = $response->streamedContent();
        $this->assertStringContainsString('CSV Export Test', $content);
        $this->assertStringContainsString('Export Client', $content);
        $this->assertStringContainsString('Active', $content);
        $this->assertStringContainsString('50%', $content);
    }

    public function test_export_filters_by_status(): void
    {
        $this->authenticate();
        $this->createProject(['name' => 'Active Project', 'status' => 1]);
        $this->createProject(['name' => 'Completed Project', 'status' => 2]);

        $response = $this->get(route('admin.projects.export', ['status' => 1]));

        $content = $response->streamedContent();
        $this->assertStringContainsString('Active Project', $content);
        $this->assertStringNotContainsString('Completed Project', $content);
    }

    public function test_export_filters_by_client(): void
    {
        $this->authenticate();
        $clientA = Client::create(['company' => 'Client A', 'active' => 1, 'datecreated' => now()]);
        $clientB = Client::create(['company' => 'Client B', 'active' => 1, 'datecreated' => now()]);

        $this->createProject(['name' => 'Project A', 'clientid' => $clientA->userid]);
        $this->createProject(['name' => 'Project B', 'clientid' => $clientB->userid]);

        $response = $this->get(route('admin.projects.export', ['client' => $clientA->userid]));
        $content = $response->streamedContent();
        $this->assertStringContainsString('Project A', $content);
        $this->assertStringNotContainsString('Project B', $content);
    }

    public function test_export_filters_by_search(): void
    {
        $this->authenticate();
        $this->createProject(['name' => 'Alpha Project']);
        $this->createProject(['name' => 'Beta Project']);

        $response = $this->get(route('admin.projects.export', ['search' => 'Alpha']));
        $content = $response->streamedContent();
        $this->assertStringContainsString('Alpha Project', $content);
        $this->assertStringNotContainsString('Beta Project', $content);
    }

    public function test_export_requires_authentication(): void
    {
        $response = $this->get(route('admin.projects.export'));
        $response->assertRedirect(route('login.staff'));
    }
}
