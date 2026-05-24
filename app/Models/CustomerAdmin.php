<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerAdmin extends Model
{
    protected $table = 'tblcustomer_admins';
    public $timestamps = false;
    protected $fillable = ['staff_id', 'customer_id', 'date_assigned'];

    protected $casts = [
        'date_assigned' => 'datetime'
    ];
    public function staff(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_id', 'staffid');
    }

    public function client(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Client::class, 'customer_id', 'userid');
    }
}

