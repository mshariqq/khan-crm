<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vault extends Model
{
    protected $table = 'tblvault';
    public $timestamps = true;
    protected $fillable = [
        'id', 'customer_id', 'server_address', 'port', 'username', 'password', 
        'description', 'creator', 'creator_name', 'visibility', 'share_in_projects', 
        'last_updated', 'last_updated_from', 'date_created'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'customer_id', 'userid');
    }

    public function creator()
    {
        return $this->belongsTo(Staff::class, 'creator', 'staffid');
    }
}
