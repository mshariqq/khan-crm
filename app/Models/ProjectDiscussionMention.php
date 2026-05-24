<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectDiscussionMention extends Model
{
    protected $table = 'tblproject_discussion_mentions';

    protected $fillable = [
        'discussion_id', 'comment_id', 'mentioned_staff_id', 'read_at'
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function discussion()
    {
        return $this->belongsTo(ProjectDiscussion::class, 'discussion_id', 'id');
    }

    public function comment()
    {
        return $this->belongsTo(ProjectDiscussionComment::class, 'comment_id', 'id');
    }

    public function mentionedStaff()
    {
        return $this->belongsTo(Staff::class, 'mentioned_staff_id', 'staffid');
    }
}
