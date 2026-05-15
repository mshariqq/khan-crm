<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $table = 'tblleads';
    public $timestamps = true;
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'dateadded' => 'datetime',
        'lastcontact' => 'datetime',
        'dateassigned' => 'date',
        'last_status_change' => 'datetime',
        'date_converted' => 'datetime',
        'lead_value' => 'decimal:2',
        'lost' => 'boolean',
        'junk' => 'boolean',
        'is_imported_from_email_integration' => 'boolean',
        'is_public' => 'boolean',
    ];
    
    protected $fillable = [
        'id', 'hash', 'name', 'title', 'company', 'description', 'country', 'zip', 'city', 'state',
        'address', 'assigned', 'dateadded', 'from_form_id', 'status', 'source', 'lastcontact',
        'dateassigned', 'last_status_change', 'addedfrom', 'email', 'website', 'leadorder',
        'phonenumber', 'date_converted', 'lost', 'junk', 'last_lead_status',
        'is_imported_from_email_integration', 'email_integration_uid', 'is_public',
        'default_language', 'client_id', 'lead_value'
    ];

    public function assignedStaff()
    {
        return $this->belongsTo(Staff::class, 'assigned', 'staffid');
    }

    public function creator()
    {
        return $this->belongsTo(Staff::class, 'addedfrom', 'staffid');
    }

    public function statusRel()
    {
        return $this->belongsTo(LeadStatus::class, 'status', 'id');
    }

    public function sourceRel()
    {
        return $this->belongsTo(LeadSource::class, 'source', 'id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'userid');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country', 'country_id');
    }

    public function countryRel()
    {
        return $this->belongsTo(Country::class, 'country', 'country_id');
    }

    public function activityLogs()
    {
        return $this->hasMany(LeadActivityLog::class, 'leadid', 'id');
    }

    public function integrationEmails()
    {
        return $this->hasMany(LeadIntegrationEmail::class, 'leadid', 'id');
    }

    public function notes()
    {
        return $this->hasMany(Note::class, 'rel_id', 'id')->where('rel_type', 'lead');
    }

    public function reminders()
    {
        return $this->hasMany(Reminder::class, 'rel_id', 'id')->where('rel_type', 'lead');
    }

    public function gdprRequests()
    {
        return $this->hasMany(GdprRequest::class, 'lead_id', 'id');
    }

    public function proposals()
    {
        return $this->hasMany(Proposal::class, 'rel_id', 'id')->where('rel_type', 'lead');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'rel_id', 'id')->where('rel_type', 'lead');
    }
}
