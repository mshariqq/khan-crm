<?php

namespace App\Events;

use App\Models\Milestone;
use Illuminate\Foundation\Events\Dispatchable;

class MilestoneCompleted
{
    use Dispatchable;

    public Milestone $milestone;

    public function __construct(Milestone $milestone)
    {
        $this->milestone = $milestone;
    }
}
