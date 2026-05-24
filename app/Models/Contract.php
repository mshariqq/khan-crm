<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contract extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'tblcontracts';

    protected $fillable = [
        'content', 'description', 'subject', 'client', 'datestart', 'dateend',
        'contract_type', 'project_id', 'addedfrom', 'dateadded', 'isexpirynotified',
        'contract_value', 'trash', 'not_visible_to_client', 'hash', 'signed',
        'signature', 'marked_as_signed', 'acceptance_firstname', 'acceptance_lastname',
        'acceptance_email', 'acceptance_date', 'acceptance_ip', 'short_link',
        'last_sent_at', 'contacts_sent_to', 'last_sign_reminder_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'datestart' => 'date',
        'dateend' => 'date',
        'dateadded' => 'datetime',
        'acceptance_date' => 'datetime',
        'last_sent_at' => 'datetime',
        'last_sign_reminder_at' => 'datetime',
        'contract_value' => 'decimal:2',
        'isexpirynotified' => 'boolean',
        'trash' => 'boolean',
        'not_visible_to_client' => 'boolean',
        'signed' => 'boolean',
        'marked_as_signed' => 'boolean',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client', 'userid');
    }

    public function creator()
    {
        return $this->belongsTo(Staff::class, 'addedfrom', 'staffid');
    }
}
