<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KnowledgeBase extends Model
{
    protected $table = 'tblknowledge_base';
    protected $primaryKey = 'articleid';
    public $timestamps = true;
    protected $fillable = ['articleid', 'articlegroup', 'subject', 'description', 'slug', 'active', 'datecreated', 'article_order', 'staff_article'];

    public function group()
    {
        return $this->belongsTo(KnowledgeBaseGroup::class, 'articlegroup', 'groupid');
    }

    public function feedbacks()
    {
        return $this->hasMany(KnowledgeBaseArticleFeedback::class, 'articleid', 'articleid');
    }
}
