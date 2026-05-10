<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstimateRequestForm extends Model
{
    protected $table = 'tblestimate_request_forms';
    public $timestamps = true;
    protected $fillable = [
        'id', 'form_key', 'type', 'name', 'form_data', 'recaptcha', 'status', 
        'submit_btn_name', 'submit_btn_bg_color', 'submit_btn_text_color', 
        'success_submit_msg', 'submit_action', 'submit_redirect_url', 'language', 
        'dateadded', 'notify_type', 'notify_ids', 'responsible', 'notify_request_submitted'
    ];
}
