<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebToLead extends Model
{
    protected $table = 'tblweb_to_lead';
    public $timestamps = true;
    protected $fillable = [
        'id', 'form_key', 'lead_source', 'lead_status', 'notify_lead_imported', 
        'notify_type', 'notify_ids', 'responsible', 'name', 'form_data', 'recaptcha', 
        'submit_btn_name', 'submit_btn_text_color', 'submit_btn_bg_color', 
        'success_submit_msg', 'submit_action', 'lead_name_prefix', 'submit_redirect_url', 
        'language', 'allow_duplicate', 'mark_public', 'track_duplicate_field', 
        'track_duplicate_field_and', 'create_task_on_duplicate', 'dateadded'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'dateadded' => 'datetime'
    ];
    public function source()
    {
        return $this->belongsTo(LeadSource::class, 'lead_source', 'id');
    }

    public function status()
    {
        return $this->belongsTo(LeadStatus::class, 'lead_status', 'id');
    }

    public function responsibleStaff()
    {
        return $this->belongsTo(Staff::class, 'responsible', 'staffid');
    }
}
