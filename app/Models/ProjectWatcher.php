<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectWatcher extends Model
{
    protected $table = 'tblproject_watchers';

    protected $fillable = ['project_id', 'staff_id'];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id', 'staffid');
    }
}
