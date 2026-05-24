<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectDiscussionCategory extends Model
{
    protected $table = 'tblproject_discussion_categories';

    protected $fillable = ['name', 'color'];
}
