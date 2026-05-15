<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proposal extends Model
{
    protected $table = 'tblproposals';
    public $timestamps = true;
    protected $fillable = [
        'subject', 'content', 'addedfrom', 'datecreated', 'total', 'subtotal', 
        'total_tax', 'adjustment', 'discount_percent', 'discount_total', 'discount_type', 
        'show_quantity_as', 'currency', 'open_till', 'date', 'rel_id', 'rel_type', 
        'assigned', 'hash', 'proposal_to', 'project_id', 'country', 'zip', 'state', 'city', 
        'address', 'email', 'phone', 'allow_comments', 'status', 'estimate_id', 
        'invoice_id', 'date_converted', 'pipeline_order', 'is_expiry_notified', 
        'acceptance_firstname', 'acceptance_lastname', 'acceptance_email', 
        'acceptance_date', 'acceptance_ip', 'signature', 'short_link'
    ];

    public function creator()
    {
        return $this->belongsTo(Staff::class, 'addedfrom', 'staffid');
    }

    public function assignedStaff()
    {
        return $this->belongsTo(Staff::class, 'assigned', 'staffid');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency', 'id');
    }

    public function currencyRel()
    {
        return $this->belongsTo(Currency::class, 'currency', 'id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country', 'country_id');
    }

    public function countryRel()
    {
        return $this->belongsTo(Country::class, 'country', 'country_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    public function estimate()
    {
        return $this->belongsTo(Estimate::class, 'estimate_id', 'id');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'id');
    }

    public function items()
    {
        return $this->hasMany(Itemable::class, 'rel_id', 'id')->where('rel_type', 'proposal');
    }

    public function comments()
    {
        return $this->hasMany(ProposalComment::class, 'proposalid', 'id');
    }

    public function notes()
    {
        return $this->hasMany(Note::class, 'rel_id', 'id')->where('rel_type', 'proposal');
    }

    public function reminders()
    {
        return $this->hasMany(Reminder::class, 'rel_id', 'id')->where('rel_type', 'proposal');
    }

    public function relatable()
    {
        return $this->morphTo(__FUNCTION__, 'rel_type', 'rel_id');
    }
}
