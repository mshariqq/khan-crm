<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketPredefinedReply extends Model
{
    protected $table = 'tbltickets_predefined_replies';
    public $timestamps = true;
    protected $fillable = ['id', 'name', 'message'];
}
