<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoicePaymentRecord extends Model
{
    protected $table = 'tblinvoicepaymentrecords';
    public $timestamps = true;
    protected $fillable = ['id', 'invoiceid', 'amount', 'paymentmode', 'paymentmethod', 'date', 'daterecorded', 'note', 'transactionid'];
}
