<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketPipeLog extends Model
{
    protected $table = 'tbltickets_pipe_log';
    public $timestamps = true;
    protected $fillable = ['id', 'date', 'email_to', 'name', 'subject', 'message', 'email', 'status'];
}
