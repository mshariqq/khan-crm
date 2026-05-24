<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAutoLogin extends Model
{
    protected $table = 'tbluser_auto_login';
    protected $primaryKey = 'key_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;
    protected $fillable = ['key_id', 'user_id', 'user_agent', 'last_ip', 'last_login', 'staff'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'last_login' => 'datetime'
    ];
    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff', 'staffid');
    }
}
