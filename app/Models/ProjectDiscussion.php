<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectDiscussion extends Model
{
    protected $table = 'tblprojectdiscussions';
    public $timestamps = true;
    protected $fillable = [
        'id', 'project_id', 'subject', 'description', 'show_to_customer', 
        'datecreated', 'last_activity', 'staff_id', 'contact_id'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    public function comments()
    {
        return $this->hasMany(ProjectDiscussionComment::class, 'discussion_id', 'id');
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id', 'staffid');
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id', 'id');
    }
}
