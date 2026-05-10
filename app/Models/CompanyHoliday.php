<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyHoliday extends Model
{
    protected $table = 'tblcompany_holidays';
    protected $fillable = ['name', 'date', 'description'];
}
