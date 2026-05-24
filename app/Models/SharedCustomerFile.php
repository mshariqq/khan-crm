<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SharedCustomerFile extends Model
{
    protected $table = 'tblshared_customer_files';
    public $timestamps = true;
    protected $fillable = ['file_id', 'contact_id'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}
