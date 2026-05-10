<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffDepartment extends Model
{
    protected $table = 'tblstaff_departments';
    protected $primaryKey = 'staffdepartmentid';
    public $timestamps = true;
    protected $fillable = ['staffdepartmentid', 'staffid', 'departmentid'];

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staffid', 'staffid');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'departmentid', 'departmentid');
    }
}
