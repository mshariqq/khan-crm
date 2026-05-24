<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectPhase extends Model
{
    protected $table = 'tblproject_phases';

    protected $fillable = [
        'project_id', 'name', 'description', 'start_date',
        'due_date', 'phase_order', 'color', 'status'
    ];

    protected $casts = [
        'start_date' => 'date',
        'due_date' => 'date',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    public function milestones()
    {
        return $this->hasMany(Milestone::class, 'phase_id', 'id');
    }
}
