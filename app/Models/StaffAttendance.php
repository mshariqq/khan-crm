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

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'date' => 'datetime',
        'clock_in' => 'datetime',
        'clock_out' => 'datetime'
    ];
    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staffid', 'staffid');
    }
}
