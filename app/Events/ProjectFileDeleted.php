<?php

namespace App\Events;

use App\Models\ProjectFile;
use Illuminate\Foundation\Events\Dispatchable;

class ProjectFileDeleted
{
    use Dispatchable;

    public ProjectFile $file;

    public function __construct(ProjectFile $file)
    {
        $this->file = $file;
    }
}
