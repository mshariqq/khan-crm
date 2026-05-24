<?php

namespace Database\Factories;

use App\Models\PinnedProject;
use App\Models\Project;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;

class PinnedProjectFactory extends Factory
{
    protected $model = PinnedProject::class;

    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'staff_id' => Staff::factory(),
        ];
    }
}
