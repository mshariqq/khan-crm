<?php

namespace App\Events;

use App\Models\Project;
use Illuminate\Foundation\Events\Dispatchable;

class ProjectStatusChanged
{
    use Dispatchable;

    public Project $project;
    public int $oldStatus;
    public int $newStatus;

    public function __construct(Project $project, int $oldStatus, int $newStatus)
    {
        $this->project = $project;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }
}
