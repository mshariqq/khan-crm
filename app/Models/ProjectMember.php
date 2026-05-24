<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectMember extends Model
{
    protected $table = 'tblproject_members';
    public $timestamps = true;
    protected $fillable = ['id', 'project_id', 'staff_id'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
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
