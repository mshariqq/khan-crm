<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketAttachment extends Model
{
    protected $table = 'tblticket_attachments';
    public $timestamps = true;
    protected $fillable = ['id', 'ticketid', 'replyid', 'file_name', 'filetype', 'dateadded'];
}
