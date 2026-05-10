<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketStatus extends Model
{
    protected $table = 'tbltickets_status';
    protected $primaryKey = 'ticketstatusid';
    public $timestamps = true;
    protected $fillable = ['ticketstatusid', 'name', 'isdefault', 'statuscolor', 'statusorder'];
}
