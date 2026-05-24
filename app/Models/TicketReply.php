<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketReply extends Model
{
    protected $table = 'tblticket_replies';
    public $timestamps = true;
    protected $fillable = ['id', 'ticketid', 'userid', 'contactid', 'name', 'email', 'date', 'message', 'attachment', 'admin', 'message_id', 'in_reply_to', 'references'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'date' => 'datetime'
    ];
    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticketid', 'ticketid');
    }
}
