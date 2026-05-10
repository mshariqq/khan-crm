<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Contact extends Authenticatable
{
    use Notifiable;

    protected $table = 'tblcontacts';
    public $timestamps = true;
    
    protected $fillable = [
        'userid', 'is_primary', 'firstname', 'lastname', 'email', 'phonenumber', 'title', 
        'datecreated', 'password', 'new_pass_key', 'new_pass_key_requested', 'email_verified_at', 
        'email_verification_key', 'email_verification_sent_at', 'last_ip', 'last_login', 
        'last_password_change', 'active', 'profile_image', 'direction', 'invoice_emails', 
        'estimate_emails', 'credit_note_emails', 'contract_emails', 'task_emails', 
        'project_emails', 'ticket_emails'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'userid', 'userid');
    }
}
