<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomField extends Model
{
    protected $table = 'tblcustomfields';
    public $timestamps = true;
    protected $fillable = [
        'id', 'fieldto', 'name', 'slug', 'required', 'type', 'options', 'display_inline', 
        'field_order', 'active', 'show_on_pdf', 'show_on_ticket_form', 'only_admin', 
        'show_on_table', 'show_on_client_portal', 'disalow_client_to_edit', 'bs_column', 'default_value'
    ];
}
