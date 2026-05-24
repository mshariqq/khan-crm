<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomersGroup extends Model
{
    protected $table = 'tblcustomers_groups';
    public $timestamps = true;
    protected $fillable = ['id', 'name'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}
