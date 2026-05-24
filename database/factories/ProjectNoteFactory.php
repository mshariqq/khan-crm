<?php

namespace Database\Factories;

use App\Models\ProjectNote;
use App\Models\Project;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectNoteFactory extends Factory
{
    protected $model = ProjectNote::class;

    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'title' => fake()->sentence(3),
            'content' => fake()->paragraph(),
            'staff_id' => Staff::factory(),
            'dateadded' => now(),
        ];
    }
}
