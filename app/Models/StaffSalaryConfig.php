<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffSalaryConfig extends Model
{
    protected $table = 'tblstaff_salary_config';
    protected $fillable = ['staffid', 'base_salary', 'fixed_allowances', 'fixed_deductions', 'effective_date'];

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staffid', 'staffid');
    }
}
