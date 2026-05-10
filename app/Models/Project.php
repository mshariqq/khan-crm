<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $table = 'tblprojects';
    public $timestamps = true;
    protected $fillable = [
        'name', 'description', 'status', 'clientid', 'billing_type', 'start_date', 
        'deadline', 'project_created', 'date_finished', 'progress', 'progress_from_tasks', 
        'project_cost', 'project_rate_per_hour', 'estimated_hours', 'addedfrom', 
        'contact_notification', 'notify_contacts'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'clientid', 'userid');
    }

    public function creator()
    {
        return $this->belongsTo(Staff::class, 'addedfrom', 'staffid');
    }

    public function staff()
    {
        return $this->belongsToMany(Staff::class, 'tblproject_members', 'project_id', 'staff_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'rel_id', 'id')->where('rel_type', 'project');
    }

    public function activities()
    {
        return $this->hasMany(ProjectActivity::class, 'project_id', 'id');
    }

    public function files()
    {
        return $this->hasMany(ProjectFile::class, 'project_id', 'id');
    }

    public function notes()
    {
        return $this->hasMany(ProjectNote::class, 'project_id', 'id');
    }

    public function settings()
    {
        return $this->hasMany(ProjectSetting::class, 'project_id', 'id');
    }

    public function discussions()
    {
        return $this->hasMany(ProjectDiscussion::class, 'project_id', 'id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'project_id', 'id');
    }

    public function estimates()
    {
        return $this->hasMany(Estimate::class, 'project_id', 'id');
    }

    public function proposals()
    {
        return $this->hasMany(Proposal::class, 'project_id', 'id');
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'project_id', 'id');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'project_id', 'id');
    }

    public function milestones()
    {
        return $this->hasMany(Milestone::class, 'project_id', 'id');
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'project_id', 'id');
    }

    public function pinnedBy()
    {
        return $this->belongsToMany(Staff::class, 'tblpinned_projects', 'project_id', 'staff_id');
    }
}
