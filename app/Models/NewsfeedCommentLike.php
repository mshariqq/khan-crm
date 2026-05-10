<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsfeedCommentLike extends Model
{
    protected $table = 'tblnewsfeed_comment_likes';
    public $timestamps = true;
    protected $fillable = ['id', 'postid', 'commentid', 'userid', 'dateliked'];
}
