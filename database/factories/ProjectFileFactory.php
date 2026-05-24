<?php

namespace Database\Factories;

use App\Models\ProjectFile;
use App\Models\Project;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFileFactory extends Factory
{
    protected $model = ProjectFile::class;

    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'file_name' => 'projects/1/' . fake()->uuid() . '.' . fake()->fileExtension(),
            'original_file_name' => fake()->word() . '.' . fake()->fileExtension(),
            'filetype' => fake()->fileExtension(),
            'file_size' => fake()->numberBetween(1024, 10485760),
            'subject' => fake()->sentence(3),
            'staffid' => Staff::factory(),
            'dateadded' => now(),
            'visible_to_customer' => true,
        ];
    }
}
