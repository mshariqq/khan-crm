<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMode extends Model
{
    protected $table = 'tblpayment_modes';
    public $timestamps = true;
    protected $fillable = ['id', 'name', 'description', 'show_on_pdf', 'invoices_only', 'expenses_only', 'selected_by_default', 'active'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'show_on_pdf' => 'boolean',
        'active' => 'boolean'
    ];
}
