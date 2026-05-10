<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffPayslip extends Model
{
    protected $table = 'tblstaff_payslips';
    protected $fillable = [
        'staffid', 'month', 'year', 'basic', 'total_additions', 'total_deductions', 'net_amount', 'status', 'payment_date'
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staffid', 'staffid');
    }

    public function items()
    {
        return $this->hasMany(PayslipItem::class, 'payslip_id', 'id');
    }
}
