<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskChecklistTemplate extends Model
{
    protected $table = 'tbltasks_checklist_templates';
    public $timestamps = true;
    protected $fillable = ['id', 'description'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}
