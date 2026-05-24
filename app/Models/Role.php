<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $table = 'tblroles';
    protected $primaryKey = 'roleid';
    public $timestamps = true;
    protected $fillable = ['roleid', 'name', 'permissions', 'is_admin'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'is_admin' => 'boolean'
    ];

    public function staff(): HasMany
    {
        return $this->hasMany(Staff::class, 'role', 'roleid');
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'tblrole_permissions', 'role_id', 'permission_id');
    }

    public function permissionAssignments(): HasMany
    {
        return $this->hasMany(RolePermission::class, 'role_id', 'roleid');
    }

    public function getPermissionSlugs(): array
    {
        $slugs = [];

        // Try pivot table first
        $pivotSlugs = $this->permissions()->pluck('slug')->toArray();
        if (!empty($pivotSlugs)) {
            $slugs = $pivotSlugs;
        }

        // Fallback to comma-separated column (legacy)
        if (empty($slugs) && !empty($this->permissions)) {
            $slugs = array_map('trim', explode(',', $this->permissions));
        }

        return array_unique(array_filter($slugs));
    }
}
