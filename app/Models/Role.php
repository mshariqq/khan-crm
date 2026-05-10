<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'tblroles';
    protected $primaryKey = 'roleid';
    public $timestamps = true;
    protected $fillable = ['roleid', 'name', 'permissions'];

    public function staff()
    {
        return $this->hasMany(Staff::class, 'role', 'roleid');
    }
}
