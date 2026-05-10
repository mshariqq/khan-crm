<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KnowledgeBaseArticleFeedback extends Model
{
    protected $table = 'tblknowledge_base_article_feedback';
    protected $primaryKey = 'articleanswerid';
    public $timestamps = true;
    protected $fillable = ['articleanswerid', 'articleid', 'answer', 'ip', 'date'];

    public function article()
    {
        return $this->belongsTo(KnowledgeBase::class, 'articleid', 'articleid');
    }
}
