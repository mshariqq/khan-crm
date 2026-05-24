<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskChecklistItem extends Model
{
    protected $table = 'tbltask_checklist_items';
    public $timestamps = true;
    protected $fillable = ['taskid', 'description', 'finished', 'dateadded', 'addedfrom', 'finished_from', 'list_order', 'assigned'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'dateadded' => 'datetime',
        'finished' => 'boolean'
    ];

    public function task()
    {
        return $this->belongsTo(Task::class, 'taskid', 'id');
    }
}
}
