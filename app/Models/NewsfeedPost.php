<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsfeedPost extends Model
{
    protected $table = 'tblnewsfeed_posts';
    protected $primaryKey = 'postid';
    public $timestamps = true;
    protected $fillable = ['postid', 'creator', 'datecreated', 'visibility', 'content', 'pinned', 'datepinned'];

    public function creator()
    {
        return $this->belongsTo(Staff::class, 'creator', 'staffid');
    }

    public function comments()
    {
        return $this->hasMany(NewsfeedPostComment::class, 'postid', 'postid');
    }

    public function likes()
    {
        return $this->hasMany(NewsfeedPostLike::class, 'postid', 'postid');
    }
}
