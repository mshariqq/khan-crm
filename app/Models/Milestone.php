<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Milestone extends Model
{
    protected $table = 'tblmilestones';
    public $timestamps = false;
    protected $fillable = [
        'id', 'name', 'description', 'description_visible_to_customer', 'start_date', 
        'due_date', 'project_id', 'color', 'milestone_order', 'datecreated', 'hide_from_customer'
    ];

    // Relationships
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    // Cast dates for convenience
    protected $dates = ['start_date', 'due_date', 'datecreated'];
}
