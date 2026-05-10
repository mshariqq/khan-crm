<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffAttendance extends Model
{
    protected $table = 'tblstaff_attendance';

    protected $fillable = [
        'staffid',
        'date',
        'clock_in',
        'clock_out',
        'status',
        'ip_address',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staffid', 'staffid');
    }
}
