<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketEmailIntegration extends Model
{
    protected $table = 'tbltickets_email_integration';
    
    protected $fillable = [
        'active', 'email', 'imap_server', 'encryption', 'port', 'password', 
        'check_every', 'department_id', 'priority_id', 'folder', 'last_run', 
        'only_loop_on_unseen_emails', 'delete_after_import'
    ];

    protected $casts = [
        'active' => 'boolean',
        'only_loop_on_unseen_emails' => 'boolean',
        'delete_after_import' => 'boolean',
        'last_run' => 'datetime'
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'departmentid');
    }

    public function priority()
    {
        return $this->belongsTo(TicketPriority::class, 'priority_id', 'priorityid');
    }
}
