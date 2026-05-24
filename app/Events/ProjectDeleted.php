<?php

namespace App\Events;

use App\Models\Project;
use Illuminate\Foundation\Events\Dispatchable;

class ProjectDeleted
{
    use Dispatchable;

    public int $projectId;
    public string $projectName;

    public function __construct(int $projectId, string $projectName)
    {
        $this->projectId = $projectId;
        $this->projectName = $projectName;
    }
}
