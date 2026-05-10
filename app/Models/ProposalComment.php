<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProposalComment extends Model
{
    protected $table = 'tblproposal_comments';
    public $timestamps = true;
    protected $fillable = ['id', 'content', 'proposalid', 'staffid', 'dateadded'];
}
