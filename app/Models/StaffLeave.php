<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffLeave extends Model
{
    protected $table = 'tblstaff_leaves';
    protected $fillable = [
        'staffid', 'leave_type_id', 'start_date', 'end_date', 'reason', 'status', 'approved_by'
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staffid', 'staffid');
    }

    public function type()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id', 'id');
    }

    public function approver()
    {
        return $this->belongsTo(Staff::class, 'approved_by', 'staffid');
    }

    public function getDurationAttribute()
    {
        $start = \Carbon\Carbon::parse($this->start_date);
        $end = \Carbon\Carbon::parse($this->end_date);
        return $start->diffInDays($end) + 1;
    }
}
