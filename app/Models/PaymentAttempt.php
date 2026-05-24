<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentAttempt extends Model
{
    protected $table = 'tblpayment_attempts';
    public $timestamps = true;
    protected $fillable = ['id', 'reference', 'invoice_id', 'amount', 'fee', 'payment_gateway', 'created_at'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}
