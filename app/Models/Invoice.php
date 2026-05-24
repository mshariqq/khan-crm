<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    const STATUS_UNPAID = 1;
    const STATUS_PARTIALLY_PAID = 2;
    const STATUS_PAID = 3;
    const STATUS_CANCELLED = 4;

    protected $table = 'tblinvoices';
    public $timestamps = true;
    protected $fillable = [
        'sent', 'datesend', 'clientid', 'deleted_customer_name', 'number', 'prefix', 
        'number_format', 'formatted_number', 'datecreated', 'date', 'duedate', 'currency', 
        'subtotal', 'total_tax', 'total', 'adjustment', 'addedfrom', 'hash', 'status', 
        'clientnote', 'adminnote', 'last_overdue_reminder', 'last_due_reminder', 
        'cancel_overdue_reminders', 'allowed_payment_modes', 'token', 'discount_percent', 
        'discount_total', 'discount_type', 'recurring', 'recurring_type', 'custom_recurring', 
        'cycles', 'total_cycles', 'is_recurring_from', 'last_recurring_date', 'terms', 
        'sale_agent', 'billing_street', 'billing_city', 'billing_state', 'billing_zip', 
        'billing_country', 'shipping_street', 'shipping_city', 'shipping_state', 
        'shipping_zip', 'shipping_country', 'include_shipping', 'show_shipping_on_invoice', 
        'show_quantity_as', 'project_id', 'subscription_id', 'short_link'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'datesend' => 'datetime',
        'datecreated' => 'datetime',
        'date' => 'datetime',
        'duedate' => 'datetime',
        'last_overdue_reminder' => 'datetime',
        'last_due_reminder' => 'datetime',
        'last_recurring_date' => 'datetime',
        'sent' => 'boolean',
        'cancel_overdue_reminders' => 'boolean',
        'recurring' => 'boolean',
        'custom_recurring' => 'boolean',
        'include_shipping' => 'boolean',
        'show_shipping_on_invoice' => 'boolean',
        'show_quantity_as' => 'boolean'
    ];

    protected $attributes = [
        'status' => 1
    ];
    public function client()
    {
        return $this->belongsTo(Client::class, 'clientid', 'userid');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency', 'id');
    }

    public function currencyRel()
    {
        return $this->belongsTo(Currency::class, 'currency', 'id');
    }

    public function creator()
    {
        return $this->belongsTo(Staff::class, 'addedfrom', 'staffid');
    }

    public function saleAgent()
    {
        return $this->belongsTo(Staff::class, 'sale_agent', 'staffid');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class, 'subscription_id', 'id');
    }

    public function items()
    {
        return $this->hasMany(Itemable::class, 'rel_id', 'id')->where('rel_type', 'invoice');
    }

    public function payments()
    {
        return $this->hasMany(InvoicePaymentRecord::class, 'invoiceid', 'id');
    }

    public function credits()
    {
        return $this->hasMany(Credit::class, 'invoice_id', 'id');
    }

    public function totalCredits()
    {
        return $this->credits()->sum('amount');
    }

    public function totalPaid()
    {
        return $this->payments()->sum('amount');
    }

    public function totalSettled()
    {
        return $this->totalPaid() + $this->totalCredits();
    }

    public function amountDue()
    {
        if ($this->status === self::STATUS_CANCELLED) {
            return 0;
        }
        return $this->total - $this->totalSettled();
    }

    public function checkoutLogs()
    {
        return $this->hasMany(TwoCheckoutLog::class, 'invoice_id', 'id');
    }

    public function notes()
    {
        return $this->hasMany(Note::class, 'rel_id', 'id')->where('rel_type', 'invoice');
    }

    public function reminders()
    {
        return $this->hasMany(Reminder::class, 'rel_id', 'id')->where('rel_type', 'invoice');
    }

    public function remindersList()
    {
        return $this->hasMany(Reminder::class, 'rel_id', 'id')->where('rel_type', 'invoice');
    }

    public function notesList()
    {
        return $this->hasMany(Note::class, 'rel_id', 'id')->where('rel_type', 'invoice');
    }

    public function salesActivities()
    {
        return $this->hasMany(SalesActivity::class, 'rel_id', 'id')->where('rel_type', 'invoice');
    }
}
