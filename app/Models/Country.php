<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = 'tblcountries';
    protected $primaryKey = 'country_id';
    public $timestamps = true;
    protected $fillable = ['country_id', 'iso2', 'short_name', 'long_name', 'iso3', 'numcode', 'un_member', 'calling_code', 'cctld'];
}
