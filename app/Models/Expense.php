<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $table = 'tblexpenses';
    public $timestamps = true;
    protected $fillable = [
        'category', 'currency', 'amount', 'tax', 'tax2', 'reference_no', 'note', 
        'expense_name', 'clientid', 'project_id', 'billable', 'invoiceid', 'paymentmode', 
        'date', 'recurring_type', 'repeat_every', 'recurring', 'cycles', 'total_cycles', 
        'custom_recurring', 'last_recurring_date', 'create_invoice_billable', 
        'send_invoice_to_customer', 'recurring_from', 'dateadded', 'addedfrom'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'date' => 'datetime',
        'last_recurring_date' => 'datetime',
        'dateadded' => 'datetime',
        'billable' => 'boolean',
        'recurring' => 'boolean',
        'custom_recurring' => 'boolean',
        'create_invoice_billable' => 'boolean',
        'send_invoice_to_customer' => 'boolean'
    ];
    public function client()
    {
        return $this->belongsTo(Client::class, 'clientid', 'userid');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    public function categoryRel()
    {
        return $this->belongsTo(ExpenseCategory::class, 'category', 'id');
    }

    public function currencyRel()
    {
        return $this->belongsTo(Currency::class, 'currency', 'id');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoiceid', 'id');
    }

    public function creator()
    {
        return $this->belongsTo(Staff::class, 'addedfrom', 'staffid');
    }
}
