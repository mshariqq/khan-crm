<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectActivity extends Model
{
    protected $table = 'tblproject_activity';
    public $timestamps = true;
    protected $fillable = [
        'id', 'project_id', 'staff_id', 'contact_id', 'fullname', 
        'visible_to_customer', 'description_key', 'additional_data', 'dateadded'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'dateadded' => 'datetime'
    ];
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id', 'staffid');
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id', 'id');
    }
}
