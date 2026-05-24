<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsentPurpose extends Model
{
    protected $table = 'tblconsent_purposes';
    public $timestamps = true;
    protected $fillable = ['id', 'name', 'description', 'date_created', 'last_updated'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'date_created' => 'datetime',
        'last_updated' => 'datetime'
    ];
}
