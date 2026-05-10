<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consent extends Model
{
    protected $table = 'tblconsents';
    public $timestamps = true;
    protected $fillable = ['id', 'action', 'date', 'ip', 'contact_id', 'lead_id', 'description', 'opt_in_purpose_description', 'purpose_id', 'staff_name'];
}
