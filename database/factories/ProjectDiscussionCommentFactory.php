<?php

namespace Database\Factories;

use App\Models\ProjectDiscussionComment;
use App\Models\ProjectDiscussion;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectDiscussionCommentFactory extends Factory
{
    protected $model = ProjectDiscussionComment::class;

    public function definition(): array
    {
        return [
            'discussion_id' => ProjectDiscussion::factory(),
            'discussion_type' => 'discussion',
            'content' => fake()->paragraph(),
            'staff_id' => Staff::factory(),
            'created' => now(),
        ];
    }
}
