<?php

namespace App\Services;

use App\Models\Staff;
use Illuminate\Support\Facades\Cache;

class PermissionService extends BaseService
{
    /**
     * Check if a staff member has a specific permission.
     * 
     * @param Staff $staff
     * @param string $permission The permission key (e.g., 'edit_invoice')
     * @return bool
     */
    public function hasPermission(Staff $staff, string $permission): bool
    {
        $cacheKey = "staff_perms_{$staff->staffid}";
        
        $permissions = Cache::remember($cacheKey, 3600, function () use ($staff) {
            $allPermissions = [];

            // 1. Get permissions from the Role
            $role = $staff->staffRole; 
            if ($role && $role->permissions) {
                // Assuming permissions are stored as a comma-separated list or JSON
                $rolePerms = is_array($role->permissions) 
                    ? $role->permissions 
                    : explode(',', $role->permissions);
                $allPermissions = array_merge($allPermissions, $rolePerms);
            }

            // 2. Get granular permissions from tblstaff_permissions
            $staffPerms = \DB::table('tblstaff_permissions')
                ->where('staff_id', $staff->staffid)
                ->pluck('capability')
                ->toArray();
            
            $allPermissions = array_merge($allPermissions, $staffPerms);

            return array_unique(array_map('trim', $allPermissions));
        });

        return in_array($permission, $permissions);
    }

    /**
     * Check if the staff member has a specific role.
     */
    public function hasRole(Staff $staff, int $roleId): bool
    {
        return $staff->role == $roleId;
    }
}
