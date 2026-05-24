<?php

namespace App\Events;

use App\Models\ProjectDiscussionComment;
use Illuminate\Foundation\Events\Dispatchable;

class DiscussionCommented
{
    use Dispatchable;

    public ProjectDiscussionComment $comment;

    public function __construct(ProjectDiscussionComment $comment)
    {
        $this->comment = $comment;
    }
}
