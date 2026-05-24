<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Client extends Authenticatable
{
    use Notifiable;
    protected $table = 'tblclients';
    protected $primaryKey = 'userid';
    public $timestamps = true;
    protected $fillable = [
        'userid', 'company', 'vat', 'phonenumber', 'country', 'city', 'zip', 'state', 'address', 
        'website', 'datecreated', 'active', 'leadid', 'billing_street', 'billing_city', 
        'billing_state', 'billing_zip', 'billing_country', 'shipping_street', 'shipping_city', 
        'shipping_state', 'shipping_zip', 'shipping_country', 'longitude', 'latitude', 
        'default_language', 'default_currency', 'show_primary_contact', 
        'registration_confirmed', 'addedfrom', 'customer_group_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'datecreated' => 'datetime',
        'active' => 'boolean',
        'show_primary_contact' => 'boolean',
        'registration_confirmed' => 'boolean'
    ];

    protected $attributes = [
        'active' => true
    ];
    protected $hidden = ['password'];


    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'clientid', 'userid');
    }

    public function projects()
    {
        return $this->hasMany(Project::class, 'clientid', 'userid');
    }

    public function leads()
    {
        return $this->hasMany(Lead::class, 'client_id', 'userid');
    }

    public function creditNotes()
    {
        return $this->hasMany(CreditNote::class, 'clientid', 'userid');
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'clientid', 'userid');
    }

    public function estimates()
    {
        return $this->hasMany(Estimate::class, 'clientid', 'userid');
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'clientid', 'userid');
    }

    public function gdprRequests()
    {
        return $this->hasMany(GdprRequest::class, 'clientid', 'userid');
    }

    public function vaults()
    {
        return $this->hasMany(Vault::class, 'customer_id', 'userid');
    }

    public function customerAdmins()
    {
        return $this->hasMany(CustomerAdmin::class, 'customer_id', 'userid');
    }

    public function group()
    {
        return $this->belongsTo(CustomersGroup::class, 'customer_group_id', 'id');
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class, 'userid', 'userid');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'userid', 'userid');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'rel_id', 'userid')->where('rel_type', 'client');
    }

    public function reminders()
    {
        return $this->hasMany(Reminder::class, 'rel_id', 'userid')->where('rel_type', 'client');
    }

    public function proposals()
    {
        return $this->hasMany(Proposal::class, 'rel_id', 'userid')->where('rel_type', 'customer');
    }

    public function notes()
    {
        return $this->hasMany(Note::class, 'rel_id', 'userid')->where('rel_type', 'client');
    }

    public function notificationsSent()
    {
        return $this->hasMany(Notification::class, 'fromclientid', 'userid');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country', 'country_id');
    }

    public function primaryContact()
    {
        return $this->hasOne(Contact::class, 'userid', 'userid')->where('is_primary', 1);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'default_currency', 'id');
    }

    public function originLead()
    {
        return $this->belongsTo(Lead::class, 'leadid', 'id');
    }
}
