<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskChecklistItem extends Model
{
    protected $table = 'tbltask_checklist_items';
    public $timestamps = true;
    protected $fillable = ['id', 'taskid', 'description', 'finished', 'dateadded', 'addedfrom', 'finished_from', 'list_order', 'assigned'];
}
