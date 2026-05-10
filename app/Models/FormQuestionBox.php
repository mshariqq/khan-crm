<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormQuestionBox extends Model
{
    protected $table = 'tblform_question_box';
    protected $primaryKey = 'boxid';
    public $timestamps = true;
    protected $fillable = ['boxid', 'boxtype', 'questionid'];
}
