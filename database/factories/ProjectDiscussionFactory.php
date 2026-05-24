<?php

namespace Database\Factories;

use App\Models\ProjectDiscussion;
use App\Models\Project;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectDiscussionFactory extends Factory
{
    protected $model = ProjectDiscussion::class;

    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'subject' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'show_to_customer' => true,
            'datecreated' => now(),
            'staff_id' => Staff::factory(),
            'last_activity' => now(),
        ];
    }
}
