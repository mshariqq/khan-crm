<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoicePaymentRecord extends Model
{
    protected $table = 'tblinvoicepaymentrecords';
    public $timestamps = true;
    protected $fillable = ['id', 'invoiceid', 'amount', 'paymentmode', 'paymentmethod', 'date', 'daterecorded', 'note', 'transactionid'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'date' => 'datetime',
        'daterecorded' => 'datetime'
    ];
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoiceid', 'id');
    }

    public function paymentMode()
    {
        return $this->belongsTo(PaymentMode::class, 'paymentmode', 'id');
    }
}
