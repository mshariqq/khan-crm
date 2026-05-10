<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserMeta extends Model
{
    protected $table = 'tbluser_meta';
    protected $primaryKey = 'umeta_id';
    public $timestamps = true;
    protected $fillable = ['umeta_id', 'staff_id', 'client_id', 'contact_id', 'meta_key', 'meta_value'];

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id', 'staffid');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'userid');
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id', 'id');
    }
}
