<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TwoCheckoutLog extends Model
{
    protected $table = 'tbltwocheckout_log';
    public $timestamps = true;
    protected $fillable = ['id', 'reference', 'invoice_id', 'amount', 'created_at', 'attempt_reference'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}
