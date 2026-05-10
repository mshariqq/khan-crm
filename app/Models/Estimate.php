<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estimate extends Model
{
    protected $table = 'tblestimates';
    public $timestamps = true;
    protected $fillable = [
        'sent', 'datesend', 'clientid', 'deleted_customer_name', 'project_id', 'number', 
        'prefix', 'number_format', 'formatted_number', 'hash', 'datecreated', 'date', 
        'expirydate', 'currency', 'subtotal', 'total_tax', 'total', 'adjustment', 'addedfrom', 
        'status', 'clientnote', 'adminnote', 'discount_percent', 'discount_total', 
        'discount_type', 'invoiceid', 'invoiced_date', 'terms', 'reference_no', 'sale_agent', 
        'billing_street', 'billing_city', 'billing_state', 'billing_zip', 'billing_country', 
        'shipping_street', 'shipping_city', 'shipping_state', 'shipping_zip', 'shipping_country', 
        'include_shipping', 'show_shipping_on_estimate', 'show_quantity_as', 'pipeline_order', 
        'is_expiry_notified', 'acceptance_firstname', 'acceptance_lastname', 'acceptance_email', 
        'acceptance_date', 'acceptance_ip', 'signature', 'short_link'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'clientid', 'userid');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    public function creator()
    {
        return $this->belongsTo(Staff::class, 'addedfrom', 'staffid');
    }

    public function saleAgent()
    {
        return $this->belongsTo(Staff::class, 'sale_agent', 'staffid');
    }

    public function currencyRel()
    {
        return $this->belongsTo(Currency::class, 'currency', 'id');
    }

    public function items()
    {
        return $this->hasMany(Itemable::class, 'rel_id', 'id')->where('rel_type', 'estimate');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoiceid', 'id');
    }

    public function notes()
    {
        return $this->hasMany(Note::class, 'rel_id', 'id')->where('rel_type', 'estimate');
    }

    public function reminders()
    {
        return $this->hasMany(Reminder::class, 'rel_id', 'id')->where('rel_type', 'estimate');
    }
}

