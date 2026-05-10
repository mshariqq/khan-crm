<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'tblnotifications';
    public $timestamps = true;
    protected $fillable = ['id', 'isread', 'isread_inline', 'date', 'description', 'fromuserid', 'fromclientid', 'from_fullname', 'touserid', 'fromcompany', 'link', 'additional_data'];

    public function receiver()
    {
        return $this->belongsTo(Staff::class, 'touserid', 'staffid');
    }

    public function senderStaff()
    {
        return $this->belongsTo(Staff::class, 'fromuserid', 'staffid');
    }

    public function senderClient()
    {
        return $this->belongsTo(Client::class, 'fromclientid', 'userid');
    }
}
