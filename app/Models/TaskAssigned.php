<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskAssigned extends Model
{
    protected $table = 'tbltask_assigned';
    public $timestamps = true;
    protected $fillable = ['id', 'staffid', 'taskid', 'assigned_from', 'is_assigned_from_contact'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'is_assigned_from_contact' => 'boolean'
    ];
}
