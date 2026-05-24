<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditNoteRefund extends Model
{
    protected $table = 'tblcreditnote_refunds';
    public $timestamps = true;
    protected $fillable = ['id', 'credit_note_id', 'staff_id', 'refunded_on', 'payment_mode', 'note', 'amount', 'created_at'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}
