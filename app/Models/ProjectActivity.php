<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectActivity extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'tblproject_activity';
    public $timestamps = true;
    protected $fillable = [
        'project_id', 'staff_id', 'contact_id', 'fullname',
        'visible_to_customer', 'description_key', 'additional_data', 'dateadded'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'dateadded' => 'datetime',
        'project_id' => 'integer',
        'staff_id' => 'integer',
        'contact_id' => 'integer',
        'visible_to_customer' => 'boolean',
        'additional_data' => 'array',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_id', 'staffid');
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'contact_id', 'id');
    }

    public function scopeForPeriod($query, $start, $end)
    {
        return $query->whereBetween('dateadded', [$start, $end]);
    }
}
