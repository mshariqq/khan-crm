<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $table = 'tbltickets';
    protected $primaryKey = 'ticketid';
    public $timestamps = true;
    protected $fillable = [
        'ticketid', 'adminreplying', 'userid', 'contactid', 'merged_ticket_id', 'email', 
        'name', 'department', 'priority', 'status', 'service', 'ticketkey', 'subject', 
        'message', 'admin', 'date', 'project_id', 'lastreply', 'clientread', 'adminread', 
        'assigned', 'staff_id_replying', 'cc', 'message_id', 'references'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'date' => 'datetime',
        'lastreply' => 'datetime',
        'adminreplying' => 'boolean',
        'clientread' => 'boolean',
        'adminread' => 'boolean'
    ];

    protected $attributes = [
        'clientread' => false,
        'adminread' => false
    ];
    public function departmentRel()
    {
        return $this->belongsTo(Department::class, 'department', 'departmentid');
    }

    public function priorityRel()
    {
        return $this->belongsTo(TicketPriority::class, 'priority', 'priorityid');
    }

    public function statusRel()
    {
        return $this->belongsTo(TicketStatus::class, 'status', 'ticketstatusid');
    }

    public function replies()
    {
        return $this->hasMany(TicketReply::class, 'ticketid', 'ticketid')->orderBy('date', 'asc');
    }

    public function attachments()
    {
        return $this->hasMany(TicketAttachment::class, 'ticketid', 'ticketid');
    }

    public function assignedStaff()
    {
        return $this->belongsTo(Staff::class, 'assigned', 'staffid');
    }

    public function replyingStaff()
    {
        return $this->belongsTo(Staff::class, 'staff_id_replying', 'staffid');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'userid', 'userid');
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contactid', 'id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }
}
