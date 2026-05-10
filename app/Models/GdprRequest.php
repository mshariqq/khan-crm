<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GdprRequest extends Model
{
    protected $table = 'tblgdpr_requests';
    public $timestamps = true;
    protected $fillable = ['id', 'clientid', 'contact_id', 'lead_id', 'request_type', 'status', 'request_date', 'request_from', 'description'];

    public function client()
    {
        return $this->belongsTo(Client::class, 'clientid', 'userid');
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id', 'id');
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id', 'id');
    }
}
