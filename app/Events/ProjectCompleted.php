<?php

namespace App\Events;

use App\Models\Project;
use Illuminate\Foundation\Events\Dispatchable;

class ProjectCompleted
{
    use Dispatchable;

    public Project $project;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }
}
