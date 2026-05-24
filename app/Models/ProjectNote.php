<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectNote extends Model
{
    protected $table = 'tblproject_notes';
    public $timestamps = true;
    protected $fillable = ['id', 'project_id', 'title', 'content', 'staff_id', 'dateadded'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'dateadded' => 'datetime'
    ];
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id', 'staffid');
    }
}
