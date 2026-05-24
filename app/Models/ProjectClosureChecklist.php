<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectClosureChecklist extends Model
{
    protected $table = 'tblproject_closure_checklists';

    protected $fillable = [
        'project_id', 'item', 'category', 'completed',
        'completed_by', 'completed_at', 'notes', 'sort_order'
    ];

    protected $casts = [
        'completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    public function completedBy()
    {
        return $this->belongsTo(Staff::class, 'completed_by', 'staffid');
    }

    public static function defaultItems(): array
    {
        return [
            ['item' => 'All tasks completed', 'category' => 'Tasks'],
            ['item' => 'All deliverables handed over', 'category' => 'Deliverables'],
            ['item' => 'Final invoice sent', 'category' => 'Financial'],
            ['item' => 'All expenses reconciled', 'category' => 'Financial'],
            ['item' => 'Project documentation archived', 'category' => 'Documentation'],
            ['item' => 'Lessons learned documented', 'category' => 'Documentation'],
            ['item' => 'Client sign-off obtained', 'category' => 'Sign-off'],
            ['item' => 'Team released from project', 'category' => 'Resources'],
            ['item' => 'Access permissions revoked', 'category' => 'Security'],
            ['item' => 'Project marked as completed', 'category' => 'System'],
        ];
    }
}
