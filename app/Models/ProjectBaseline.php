<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectBaseline extends Model
{
    protected $table = 'tblproject_baselines';

    protected $fillable = [
        'project_id', 'name', 'description', 'planned_value',
        'planned_cost', 'planned_duration_days', 'baseline_date',
        'scope_snapshot', 'schedule_snapshot', 'cost_snapshot', 'is_active'
    ];

    protected $casts = [
        'planned_value' => 'decimal:2',
        'planned_cost' => 'decimal:2',
        'baseline_date' => 'date',
        'scope_snapshot' => 'array',
        'schedule_snapshot' => 'array',
        'cost_snapshot' => 'array',
        'is_active' => 'boolean',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
