<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    const STATUS_BACKLOG = 0;
    const STATUS_PENDING = 1;
    const STATUS_IN_PROGRESS = 2;
    const STATUS_COMPLETED = 3;
    const PRIORITY_URGENT = 4;

    protected $table = 'tbltasks';
    public $timestamps = true;
    protected $fillable = [
        'name', 'description', 'priority', 'dateadded', 'startdate', 'duedate', 
        'datefinished', 'addedfrom', 'is_added_from_contact', 'status', 'recurring_type', 
        'repeat_every', 'recurring', 'is_recurring_from', 'cycles', 'total_cycles', 
        'custom_recurring', 'last_recurring_date', 'rel_id', 'rel_type', 'is_public', 
        'billable', 'billed', 'invoice_id', 'hourly_rate', 'milestone', 'kanban_order', 
        'milestone_order', 'visible_to_client', 'deadline_notified', 'tags'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'dateadded' => 'datetime',
        'startdate' => 'datetime',
        'duedate' => 'datetime',
        'datefinished' => 'datetime',
        'last_recurring_date' => 'datetime',
        'is_added_from_contact' => 'boolean',
        'recurring' => 'boolean',
        'custom_recurring' => 'boolean',
        'is_public' => 'boolean',
        'billable' => 'boolean',
        'billed' => 'boolean',
        'visible_to_client' => 'boolean',
        'deadline_notified' => 'boolean',
        'tags' => 'string'
    ];

    protected $attributes = [
        'is_public' => false,
        'billable' => false
    ];
    public function creator()
    {
        return $this->belongsTo(Staff::class, 'addedfrom', 'staffid');
    }

    public function milestoneRelation()
    {
        return $this->belongsTo(Milestone::class, 'milestone', 'id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'rel_id', 'id')->where('rel_type', 'project');
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

    public function attachments()
    {
        return $this->hasMany(File::class, 'rel_id', 'id')->where('rel_type', 'task');
    }

    public function scopeForStaff($query, $staffId)
    {
        return $query->whereHas('assignedStaff', function ($q) use ($staffId) {
            $q->where('staffid', $staffId);
        });
    }

    public function scopeByRelType($query, $relType)
    {
        return $query->where('rel_type', $relType);
    }

    public function relatable()
    {
        return $this->morphTo('relatable', 'rel_type', 'rel_id');
    }

    public function getRelatedEntityAttribute()
    {
        if (!$this->rel_type || !$this->rel_id) {
            return null;
        }

        if ($this->relationLoaded('relatable')) {
            return $this->getRelation('relatable');
        }

        return $this->relatable()->first();
    }
}
