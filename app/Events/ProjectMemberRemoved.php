<?php

namespace App\Events;

use App\Models\Project;
use App\Models\Staff;
use Illuminate\Foundation\Events\Dispatchable;

class ProjectMemberRemoved
{
    use Dispatchable;

    public Project $project;
    public Staff $staff;

    public function __construct(Project $project, Staff $staff)
    {
        $this->project = $project;
        $this->staff = $staff;
    }
}
