<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectFile extends Model
{
    protected $table = 'tblproject_files';
    public $timestamps = true;
    protected $fillable = [
        'id', 'file_name', 'original_file_name', 'subject', 'description', 'filetype', 
        'dateadded', 'last_activity', 'project_id', 'visible_to_customer', 'staffid', 
        'contact_id', 'external', 'external_link', 'thumbnail_link'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'dateadded' => 'datetime',
        'last_activity' => 'datetime'
    ];
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staffid', 'staffid');
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id', 'id');
    }
}
