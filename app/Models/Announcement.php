<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $table = 'tblannouncements';
    protected $primaryKey = 'announcementid';
    public $timestamps = true;
    protected $fillable = ['announcementid', 'name', 'message', 'showtousers', 'showtostaff', 'showname', 'dateadded', 'userid'];
}
