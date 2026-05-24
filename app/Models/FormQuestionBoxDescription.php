<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormQuestionBoxDescription extends Model
{
    protected $table = 'tblform_question_box_description';
    protected $primaryKey = 'questionboxdescriptionid';
    public $timestamps = true;
    protected $fillable = ['questionboxdescriptionid', 'description', 'boxid', 'questionid'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}
