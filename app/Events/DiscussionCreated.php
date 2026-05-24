<?php

namespace App\Events;

use App\Models\ProjectDiscussion;
use Illuminate\Foundation\Events\Dispatchable;

class DiscussionCreated
{
    use Dispatchable;

    public ProjectDiscussion $discussion;

    public function __construct(ProjectDiscussion $discussion)
    {
        $this->discussion = $discussion;
    }
}
