<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $table = 'tbltasks';
    public $timestamps = true;
    protected $fillable = [
        'id', 'name', 'description', 'priority', 'dateadded', 'startdate', 'duedate', 
        'datefinished', 'addedfrom', 'is_added_from_contact', 'status', 'recurring_type', 
        'repeat_every', 'recurring', 'is_recurring_from', 'cycles', 'total_cycles', 
        'custom_recurring', 'last_recurring_date', 'rel_id', 'rel_type', 'is_public', 
        'billable', 'billed', 'invoice_id', 'hourly_rate', 'milestone', 'kanban_order', 
        'milestone_order', 'visible_to_client', 'deadline_notified'
    ];

    public function creator()
    {
        return $this->belongsTo(Staff::class, 'addedfrom', 'staffid');
    }

    // Relationship to Milestone (foreign key column is 'milestone')
    public function milestoneRelation()
    {
        return $this->belongsTo(Milestone::class, 'milestone', 'id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'rel_id', 'id');
    }

    public function getProjectAttribute()
    {
        return $this->relatedEntity;
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'id');
    }

    public function assignedStaff()
    {
        return $this->belongsToMany(Staff::class, 'tbltask_assigned', 'taskid', 'staffid');
    }

    public function followers()
    {
        return $this->belongsToMany(Staff::class, 'tbltask_followers', 'taskid', 'staffid');
    }

    public function comments()
    {
        return $this->hasMany(TaskComment::class, 'taskid', 'id');
    }

    public function timers()
    {
        return $this->hasMany(TaskTimer::class, 'task_id', 'id');
    }

    public function checklistItems()
    {
        return $this->hasMany(TaskChecklistItem::class, 'taskid', 'id');
    }

    public function relatable()
    {
        if (!$this->rel_type || !$this->rel_id) {
            return $this->belongsTo(Project::class, 'rel_id', 'id');
        }

        return match($this->rel_type) {
            'project' => $this->belongsTo(Project::class, 'rel_id', 'id'),
            'invoice' => $this->belongsTo(Invoice::class, 'rel_id', 'id'),
            'estimate' => $this->belongsTo(Estimate::class, 'rel_id', 'id'),
            'proposal' => $this->belongsTo(Proposal::class, 'rel_id', 'id'),
            default => $this->belongsTo(Project::class, 'rel_id', 'id'),
        };
    }

    public function getRelatedEntityAttribute()
    {
        if (!$this->rel_type || !$this->rel_id) {
            return null;
        }

        return match($this->rel_type) {
            'project' => Project::find($this->rel_id),
            'invoice' => Invoice::find($this->rel_id),
            'estimate' => Estimate::find($this->rel_id),
            'proposal' => Proposal::find($this->rel_id),
            default => null,
        };
    }
}
