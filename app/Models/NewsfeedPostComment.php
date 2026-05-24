<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsfeedPostComment extends Model
{
    protected $table = 'tblnewsfeed_post_comments';
    public $timestamps = true;
    protected $fillable = ['id', 'content', 'userid', 'postid', 'dateadded'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'dateadded' => 'datetime'
    ];
    public function post()
    {
        return $this->belongsTo(NewsfeedPost::class, 'postid', 'postid');
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'userid', 'staffid');
    }

    public function likes()
    {
        return $this->hasMany(NewsfeedCommentLike::class, 'commentid', 'id');
    }
}
