<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Client;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        return [
            'name' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'status' => Project::STATUS_ACTIVE,
            'clientid' => Client::factory(),
            'billing_type' => Project::BILLING_TYPE_FIXED,
            'start_date' => now()->format('Y-m-d'),
            'deadline' => now()->addDays(30)->format('Y-m-d'),
            'project_created' => now()->format('Y-m-d'),
            'project_cost' => fake()->randomFloat(2, 1000, 50000),
            'project_rate_per_hour' => fake()->randomFloat(2, 25, 200),
            'estimated_hours' => fake()->randomFloat(1, 20, 500),
            'addedfrom' => Staff::factory(),
            'progress' => fake()->numberBetween(0, 100),
            'contact_notification' => true,
            'notify_contacts' => null,
        ];
    }

    public function active(): static
    {
        return $this->state(fn(array $attrs) => ['status' => Project::STATUS_ACTIVE]);
    }

    public function completed(): static
    {
        return $this->state(fn(array $attrs) => [
            'status' => Project::STATUS_COMPLETED,
            'progress' => 100,
            'date_finished' => now(),
        ]);
    }

    public function onHold(): static
    {
        return $this->state(fn(array $attrs) => ['status' => Project::STATUS_ON_HOLD]);
    }

    public function cancelled(): static
    {
        return $this->state(fn(array $attrs) => ['status' => Project::STATUS_CANCELLED]);
    }

    public function withProgressFromTasks(): static
    {
        return $this->state(fn(array $attrs) => ['progress_from_tasks' => true]);
    }
}
