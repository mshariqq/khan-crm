<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $table = 'tblcurrencies';
    public $timestamps = true;
    protected $fillable = ['id', 'symbol', 'name', 'decimal_separator', 'thousand_separator', 'placement', 'isdefault'];
}
