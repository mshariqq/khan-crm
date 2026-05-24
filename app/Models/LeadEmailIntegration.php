<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadEmailIntegration extends Model
{
    protected $table = 'tblleads_email_integration';
    public $timestamps = true;
    protected $fillable = [
        'id', 'active', 'email', 'imap_server', 'password', 'check_every', 'responsible', 
        'lead_source', 'lead_status', 'encryption', 'folder', 'last_run', 'notify_lead_imported', 
        'notify_lead_contact_more_times', 'notify_type', 'notify_ids', 'mark_public', 
        'only_loop_on_unseen_emails', 'delete_after_import', 'create_task_if_customer'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'last_run' => 'datetime',
        'active' => 'boolean'
    ];
}
