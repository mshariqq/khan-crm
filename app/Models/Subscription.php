<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $table = 'tblsubscriptions';
    public $timestamps = true;
    protected $fillable = [
        'name', 'description', 'description_in_item', 'clientid', 'date', 'terms', 
        'currency', 'tax_id', 'stripe_tax_id', 'tax_id_2', 'stripe_tax_id_2', 
        'stripe_plan_id', 'stripe_subscription_id', 'next_billing_cycle', 'ends_at', 
        'status', 'quantity', 'project_id', 'hash', 'created', 'created_from', 
        'date_subscribed', 'in_test_environment', 'last_sent_at'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'clientid', 'userid');
    }

    public function currencyRel()
    {
        return $this->belongsTo(Currency::class, 'currency', 'id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'subscription_id', 'id');
    }

    public function creator()
    {
        return $this->belongsTo(Staff::class, 'created_from', 'staffid');
    }
}
