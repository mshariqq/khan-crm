<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    /**
     * Create comprehensive roles for CRM/ERP system
     */
    public function run(): void
    {
        // Get all permission slugs
        $allPermissionSlugs = \DB::table('tblpermissions')->pluck('slug')->toArray();
        
        // Define role permissions
        $roles = [
            [
                'roleid' => 1,
                'name' => 'Super Administrator',
                'description' => 'Full system access and all permissions',
                'permissions' => $allPermissionSlugs
            ],
            [
                'roleid' => 2,
                'name' => 'Administrator',
                'description' => 'Full system access with some restrictions',
                'permissions' => $allPermissionSlugs
            ],
            [
                'roleid' => 3,
                'name' => 'Manager',
                'description' => 'Manages projects, team, and financials',
                'permissions' => $this->getManagerPermissions($allPermissionSlugs)
            ],
            [
                'roleid' => 4,
                'name' => 'Project Manager',
                'description' => 'Manages projects and team tasks',
                'permissions' => $this->getProjectManagerPermissions($allPermissionSlugs)
            ],
            [
                'roleid' => 5,
                'name' => 'Sales Representative',
                'description' => 'Manages leads, proposals and client relations',
                'permissions' => $this->getSalesPermissions($allPermissionSlugs)
            ],
            [
                'roleid' => 6,
                'name' => 'Staff Member',
                'description' => 'Standard staff member with limited access',
                'permissions' => [
                    'dashboard_view',
                    'tasks_view', 'tasks_create', 'tasks_edit',
                    'projects_view',
                    'clients_view',
                    'contacts_view',
                    'tickets_view', 'tickets_create',
                    'knowledgebase_view',
                ]
            ],
            [
                'roleid' => 7,
                'name' => 'Support Agent',
                'description' => 'Handles customer support tickets',
                'permissions' => [
                    'dashboard_view',
                    'tickets_view', 'tickets_create', 'tickets_edit',
                    'contacts_view',
                    'knowledgebase_view',
                ]
            ],
        ];

        foreach ($roles as $role) {
            DB::table('tblroles')->updateOrInsert(
                ['roleid' => $role['roleid']],
                [
                    'name' => $role['name'],
                    'permissions' => is_array($role['permissions']) ? implode(',', $role['permissions']) : $role['permissions']
                ]
            );
        }
    }
    
    /**
     * Get Manager role permissions
     */
    private function getManagerPermissions($allPermissionSlugs)
    {
        $managerPerms = [];
        $allowedModules = ['projects', 'tasks', 'staff', 'invoices', 'estimates', 'expenses', 'proposals', 'clients', 'contacts', 'tickets'];
        
        foreach ($allPermissionSlugs as $perm) {
            $module = strtolower(substr($perm, 0, strpos($perm, '_')));
            if (in_array($module, $allowedModules) || strpos($perm, 'dashboard_') !== false || strpos($perm, 'reports_') !== false) {
                $managerPerms[] = $perm;
            }
        }
        
        // Remove dangerous permissions
        $dangerousPerms = ['settings_delete', 'roles_delete', 'permissions_delete', 'staff_delete', 'files_delete_all'];
        return array_diff($managerPerms, $dangerousPerms);
    }
    
    /**
     * Get Project Manager role permissions
     */
    private function getProjectManagerPermissions($allPermissionSlugs)
    {
        $pmPerms = [];
        
        foreach ($allPermissionSlugs as $perm) {
            $module = strtolower(substr($perm, 0, strpos($perm, '_')));
            if (in_array($module, ['projects', 'tasks', 'dashboard']) || strpos($perm, 'dashboard_') !== false) {
                $pmPerms[] = $perm;
            }
        }
        
        return $pmPerms;
    }
    
    /**
     * Get Sales Representative role permissions
     */
    private function getSalesPermissions($allPermissionSlugs)
    {
        $salesPerms = [];
        
        foreach ($allPermissionSlugs as $perm) {
            $module = strtolower(substr($perm, 0, strpos($perm, '_')));
            if (in_array($module, ['leads', 'proposals', 'estimates', 'clients', 'contacts', 'dashboard']) || strpos($perm, 'dashboard_') !== false) {
                $salesPerms[] = $perm;
            }
        }
        
        // Remove delete permissions
        return array_filter($salesPerms, fn($perm) => strpos($perm, '_delete') === false);
    }
}
