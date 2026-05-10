<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    protected $table = 'tblleave_types';
    protected $fillable = ['name', 'default_quota', 'is_paid'];

    public function leaves()
    {
        return $this->hasMany(StaffLeave::class, 'leave_type_id', 'id');
    }
}
