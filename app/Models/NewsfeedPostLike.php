<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsfeedPostLike extends Model
{
    protected $table = 'tblnewsfeed_post_likes';
    public $timestamps = true;
    protected $fillable = ['id', 'postid', 'userid', 'dateliked'];
}
