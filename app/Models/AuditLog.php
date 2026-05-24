<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $table = 'tblaudit_logs';
    public $timestamps = true;
    protected $fillable = ['action', 'entity_type', 'entity_id', 'description', 'old_values', 'new_values', 'staff_id', 'ip_address', 'user_agent'];
    protected $casts = ['old_values' => 'array', 'new_values' => 'array'];

    public function staff(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_id', 'staffid');
    }
}

