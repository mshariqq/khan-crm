<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MailQueue extends Model
{
    protected $table = 'tblmail_queue';
    public $timestamps = true;
    protected $fillable = ['id', 'engine', 'email', 'cc', 'bcc', 'message', 'alt_message', 'status', 'date', 'headers', 'attachments'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'date' => 'datetime'
    ];
}
