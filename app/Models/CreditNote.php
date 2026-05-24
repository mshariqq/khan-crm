<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditNote extends Model
{
    const STATUS_OPEN = 1;

    protected $table = 'tblcreditnotes';
    public $timestamps = true;
    protected $fillable = [
        'id', 'clientid', 'deleted_customer_name', 'number', 'prefix', 'number_format', 
        'formatted_number', 'datecreated', 'date', 'adminnote', 'terms', 'clientnote', 
        'currency', 'subtotal', 'total_tax', 'total', 'adjustment', 'addedfrom', 'status', 
        'project_id', 'discount_percent', 'discount_total', 'discount_type', 'billing_street', 
        'billing_city', 'billing_state', 'billing_zip', 'billing_country', 'shipping_street', 
        'shipping_city', 'shipping_state', 'shipping_zip', 'shipping_country', 
        'include_shipping', 'show_shipping_on_credit_note', 'show_quantity_as', 'reference_no'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'datecreated' => 'datetime',
        'date' => 'datetime',
        'include_shipping' => 'boolean',
        'show_shipping_on_credit_note' => 'boolean',
        'show_quantity_as' => 'boolean'
    ];

    protected $attributes = [
        'status' => 1
    ];
    public function client()
    {
        return $this->belongsTo(Client::class, 'clientid', 'userid');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    public function currencyRel()
    {
        return $this->belongsTo(Currency::class, 'currency', 'id');
    }

    public function items()
    {
        return $this->hasMany(Itemable::class, 'rel_id', 'id')->where('rel_type', 'creditnote');
    }

    public function credits()
    {
        return $this->hasMany(Credit::class, 'credit_id', 'id');
    }

    public function totalApplied()
    {
        return $this->credits()->sum('amount');
    }

    public function remainingCredit()
    {
        return $this->total - $this->totalApplied();
    }
}
