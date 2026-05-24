<?php

namespace App\Events;

use App\Models\Project;
use Illuminate\Foundation\Events\Dispatchable;

class ProjectUpdated
{
    use Dispatchable;

    public Project $project;
    public ?int $oldStatus;
    public ?int $newStatus;

    public function __construct(Project $project, ?int $oldStatus = null, ?int $newStatus = null)
    {
        $this->project = $project;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }
}
