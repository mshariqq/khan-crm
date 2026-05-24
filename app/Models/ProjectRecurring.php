<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectRecurring extends Model
{
    protected $table = 'tblproject_recurring';

    protected $fillable = [
        'template_project_id', 'recurring_type', 'repeat_every',
        'max_occurrences', 'occurrences_generated', 'next_generation_date',
        'last_generated_date', 'is_active', 'copy_team',
        'copy_milestones', 'copy_tasks'
    ];

    protected $casts = [
        'repeat_every' => 'integer',
        'max_occurrences' => 'integer',
        'occurrences_generated' => 'integer',
        'next_generation_date' => 'date',
        'last_generated_date' => 'date',
        'is_active' => 'boolean',
        'copy_team' => 'boolean',
        'copy_milestones' => 'boolean',
        'copy_tasks' => 'boolean',
    ];

    public function templateProject()
    {
        return $this->belongsTo(Project::class, 'template_project_id', 'id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
