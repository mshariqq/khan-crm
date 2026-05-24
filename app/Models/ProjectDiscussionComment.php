<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectDiscussionComment extends Model
{
    protected $table = 'tblprojectdiscussioncomments';
    public $timestamps = true;
    protected $fillable = [
        'id', 'discussion_id', 'discussion_type', 'parent', 'created', 
        'modified', 'content', 'staff_id', 'contact_id', 'fullname', 
        'file_name', 'file_mime_type'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'created' => 'datetime',
        'modified' => 'datetime'
    ];
    public function discussion()
    {
        return $this->belongsTo(ProjectDiscussion::class, 'discussion_id', 'id');
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
