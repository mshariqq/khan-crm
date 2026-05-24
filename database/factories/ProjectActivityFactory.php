<?php

namespace Database\Factories;

use App\Models\ProjectActivity;
use App\Models\Project;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectActivityFactory extends Factory
{
    protected $model = ProjectActivity::class;

    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'staff_id' => Staff::factory(),
            'description_key' => 'project_activity_project_created',
            'additional_data' => json_encode(['project_name' => fake()->sentence(2)]),
            'dateadded' => now(),
            'visible_to_customer' => 1,
        ];
    }
}
