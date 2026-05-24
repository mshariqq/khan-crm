<?php

namespace Database\Factories;

use App\Models\Milestone;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class MilestoneFactory extends Factory
{
    protected $model = Milestone::class;

    public function definition(): array
    {
        return [
            'name' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'project_id' => Project::factory(),
            'due_date' => now()->addDays(fake()->numberBetween(7, 90))->format('Y-m-d'),
            'milestone_order' => fake()->numberBetween(0, 10),
        ];
    }
}
