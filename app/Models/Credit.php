<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Credit extends Model
{
    protected $table = 'tblcredits';
    public $timestamps = true;
    protected $fillable = ['invoice_id', 'credit_id', 'staff_id', 'date', 'date_applied', 'amount'];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'id');
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id', 'staffid');
    }
}
