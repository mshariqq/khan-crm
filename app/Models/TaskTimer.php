<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskTimer extends Model
{
    protected $table = 'tbltaskstimers';
    public $timestamps = true;
    protected $fillable = ['task_id', 'start_time', 'end_time', 'staff_id', 'hourly_rate', 'note', 'is_billable'];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id', 'staffid');
    }
}
