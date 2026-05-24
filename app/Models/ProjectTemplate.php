<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectTemplate extends Model
{
    protected $table = 'tblproject_templates';

    protected $fillable = [
        'name', 'description', 'category', 'estimated_budget',
        'milestones', 'tasks', 'team_roles', 'checklists',
        'is_public', 'created_by'
    ];

    protected $casts = [
        'milestones' => 'array',
        'tasks' => 'array',
        'team_roles' => 'array',
        'checklists' => 'array',
        'is_public' => 'boolean',
        'estimated_budget' => 'decimal:2',
    ];

    public function creator()
    {
        return $this->belongsTo(Staff::class, 'created_by', 'staffid');
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }
}
