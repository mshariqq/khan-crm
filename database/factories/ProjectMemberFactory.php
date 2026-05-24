<?php

namespace Database\Factories;

use App\Models\ProjectMember;
use App\Models\Project;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectMemberFactory extends Factory
{
    protected $model = ProjectMember::class;

    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'staff_id' => Staff::factory(),
        ];
    }
}
