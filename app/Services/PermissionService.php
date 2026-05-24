<?php

namespace App\Services;

use App\Models\Staff;
use Illuminate\Support\Facades\Cache;

class PermissionService extends BaseService
{
    /**
     * Check if a staff member has a specific permission.
     * Deny mechanism: staff-specific permissions prefixed with '!' (e.g., '!invoices_delete')
     * will override role-granted permissions.
     * 
     * @param Staff $staff
     * @param string $permission The permission key (e.g., 'edit_invoice')
     * @return bool
     */
    public function hasPermission(Staff $staff, string $permission): bool
    {
        $cacheKey = "staff_perms_{$staff->staffid}";
        
        $cached = Cache::remember($cacheKey, 3600, function () use ($staff) {
            $granted = [];
            $denied = [];

            // 1. Get permissions from the Role (pivot table preferred, fallback to comma-separated)
            $role = $staff->staffRole; 
            if ($role) {
                $rolePerms = $role->getPermissionSlugs();
                foreach ($rolePerms as $perm) {
                    $perm = trim($perm);
                    if (!$perm) continue;
                    $granted[] = $perm;
                }
            }

            // 2. Get granular permissions from tblstaff_permissions
            $staffPerms = \DB::table('tblstaff_permissions')
                ->where('staff_id', $staff->staffid)
                ->pluck('capability')
                ->toArray();
            
            foreach ($staffPerms as $perm) {
                $perm = trim($perm);
                if (!$perm) continue;
                if (str_starts_with($perm, '!')) {
                    $denied[] = substr($perm, 1);
                } else {
                    $granted[] = $perm;
                }
            }

            return [
                'granted' => array_unique($granted),
                'denied' => array_unique($denied),
            ];
        });

        // Deny takes precedence over grant
        if (in_array($permission, $cached['denied'])) {
            return false;
        }

        return in_array($permission, $cached['granted']);
    }

    /**
     * Check if the staff member has a specific role.
     */
    public function hasRole(Staff $staff, int $roleId): bool
    {
        return $staff->role == $roleId;
    }

    /**
     * Clear the permission cache for a specific staff member or all staff.
     */
    public function clearCache(?int $staffId = null): void
    {
        if ($staffId) {
            Cache::forget("staff_perms_{$staffId}");
        } else {
            $staffIds = \DB::table('tblstaff')->pluck('staffid');
            foreach ($staffIds as $id) {
                Cache::forget("staff_perms_{$id}");
            }
        }
    }
}
