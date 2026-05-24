<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table = 'tbldepartments';
    protected $primaryKey = 'departmentid';
    public $timestamps = true;
    protected $fillable = [
        'departmentid', 'name', 'imap_username', 'email', 'email_from_header', 'host', 
        'password', 'encryption', 'folder', 'delete_after_import', 'calendar_id', 'hidefromclient'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    public function staff()
    {
        return $this->belongsToMany(Staff::class, 'tblstaff_departments', 'departmentid', 'staffid');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'department', 'departmentid');
    }
}
