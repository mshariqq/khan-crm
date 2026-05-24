<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectResourceAllocation extends Model
{
    protected $table = 'tblproject_resource_allocations';

    protected $fillable = [
        'project_id', 'staff_id', 'role', 'allocation_percentage',
        'start_date', 'end_date', 'notes'
    ];

    protected $casts = [
        'allocation_percentage' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
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
