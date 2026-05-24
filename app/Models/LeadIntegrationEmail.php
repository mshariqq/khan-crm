<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadIntegrationEmail extends Model
{
    protected $table = 'tbllead_integration_emails';
    public $timestamps = true;
    protected $fillable = ['id', 'subject', 'body', 'dateadded', 'leadid', 'emailid'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'dateadded' => 'datetime'
    ];
}
