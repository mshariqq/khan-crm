<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayslipItem extends Model
{
    protected $table = 'tblpayslip_items';
    protected $fillable = ['payslip_id', 'type', 'label', 'amount'];

    public function payslip()
    {
        return $this->belongsTo(StaffPayslip::class, 'payslip_id', 'id');
    }
}
