<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffPermission extends Model
{
    protected $table = 'tblstaff_permissions';
    public $timestamps = true;
    protected $fillable = ['staff_id', 'feature', 'capability'];

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id', 'staffid');
    }
}
