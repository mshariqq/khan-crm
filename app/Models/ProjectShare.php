<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProjectShare extends Model
{
    protected $table = 'tblproject_shares';

    protected $fillable = [
        'project_id', 'token', 'email', 'access_level',
        'expires_at', 'last_accessed_at', 'access_count', 'is_active', 'created_by'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'last_accessed_at' => 'datetime',
        'is_active' => 'boolean',
        'access_count' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($share) {
            if (empty($share->token)) {
                $share->token = Str::random(64);
            }
        });
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    public function creator()
    {
        return $this->belongsTo(Staff::class, 'created_by', 'staffid');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            });
    }

    public function getIsExpiredAttribute()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function getUrlAttribute()
    {
        return route('public.projects.view', $this->token);
    }
}
