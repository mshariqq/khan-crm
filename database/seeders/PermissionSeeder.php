<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Comprehensive permissions for CRM/ERP system
     */
    public function run(): void
    {
        $permissions = [
            // Dashboard
            ['name' => 'View Dashboard', 'slug' => 'dashboard_view'],
            
            // Clients Module
            ['name' => 'View Clients', 'slug' => 'clients_view'],
            ['name' => 'Create Clients', 'slug' => 'clients_create'],
            ['name' => 'Edit Clients', 'slug' => 'clients_edit'],
            ['name' => 'Delete Clients', 'slug' => 'clients_delete'],
            
            // Contacts Module
            ['name' => 'View Contacts', 'slug' => 'contacts_view'],
            ['name' => 'Create Contacts', 'slug' => 'contacts_create'],
            ['name' => 'Edit Contacts', 'slug' => 'contacts_edit'],
            ['name' => 'Delete Contacts', 'slug' => 'contacts_delete'],
            
            // Customer Groups
            ['name' => 'View Customer Groups', 'slug' => 'customergroups_view'],
            ['name' => 'Create Customer Groups', 'slug' => 'customergroups_create'],
            ['name' => 'Edit Customer Groups', 'slug' => 'customergroups_edit'],
            ['name' => 'Delete Customer Groups', 'slug' => 'customergroups_delete'],
            
            // Projects Module
            ['name' => 'View Projects', 'slug' => 'projects_view'],
            ['name' => 'Create Projects', 'slug' => 'projects_create'],
            ['name' => 'Edit Projects', 'slug' => 'projects_edit'],
            ['name' => 'Delete Projects', 'slug' => 'projects_delete'],
            
            // Milestones
            ['name' => 'View Milestones', 'slug' => 'milestones_view'],
            ['name' => 'Create Milestones', 'slug' => 'milestones_create'],
            ['name' => 'Edit Milestones', 'slug' => 'milestones_edit'],
            ['name' => 'Delete Milestones', 'slug' => 'milestones_delete'],
            
            // Tasks Module
            ['name' => 'View Tasks', 'slug' => 'tasks_view'],
            ['name' => 'Create Tasks', 'slug' => 'tasks_create'],
            ['name' => 'Edit Tasks', 'slug' => 'tasks_edit'],
            ['name' => 'Delete Tasks', 'slug' => 'tasks_delete'],
            ['name' => 'Edit All Tasks', 'slug' => 'tasks_edit_all'],
            ['name' => 'Delete All Tasks', 'slug' => 'tasks_delete_all'],
            
            // Sales/Leads Module
            ['name' => 'View Leads', 'slug' => 'leads_view'],
            ['name' => 'Create Leads', 'slug' => 'leads_create'],
            ['name' => 'Edit Leads', 'slug' => 'leads_edit'],
            ['name' => 'Delete Leads', 'slug' => 'leads_delete'],
            
            // Finance - Invoices
            ['name' => 'View Invoices', 'slug' => 'invoices_view'],
            ['name' => 'Create Invoices', 'slug' => 'invoices_create'],
            ['name' => 'Edit Invoices', 'slug' => 'invoices_edit'],
            ['name' => 'Delete Invoices', 'slug' => 'invoices_delete'],
            
            // Finance - Estimates
            ['name' => 'View Estimates', 'slug' => 'estimates_view'],
            ['name' => 'Create Estimates', 'slug' => 'estimates_create'],
            ['name' => 'Edit Estimates', 'slug' => 'estimates_edit'],
            ['name' => 'Delete Estimates', 'slug' => 'estimates_delete'],
            
            // Finance - Credit Notes
            ['name' => 'View Credit Notes', 'slug' => 'credit_notes_view'],
            ['name' => 'Create Credit Notes', 'slug' => 'credit_notes_create'],
            ['name' => 'Edit Credit Notes', 'slug' => 'credit_notes_edit'],
            ['name' => 'Delete Credit Notes', 'slug' => 'credit_notes_delete'],
            
            // Finance - Expenses
            ['name' => 'View Expenses', 'slug' => 'expenses_view'],
            ['name' => 'Create Expenses', 'slug' => 'expenses_create'],
            ['name' => 'Edit Expenses', 'slug' => 'expenses_edit'],
            ['name' => 'Delete Expenses', 'slug' => 'expenses_delete'],
            
            // Proposals
            ['name' => 'View Proposals', 'slug' => 'proposals_view'],
            ['name' => 'Create Proposals', 'slug' => 'proposals_create'],
            ['name' => 'Edit Proposals', 'slug' => 'proposals_edit'],
            ['name' => 'Delete Proposals', 'slug' => 'proposals_delete'],
            
            // Subscriptions
            ['name' => 'View Subscriptions', 'slug' => 'subscriptions_view'],
            ['name' => 'Create Subscriptions', 'slug' => 'subscriptions_create'],
            ['name' => 'Edit Subscriptions', 'slug' => 'subscriptions_edit'],
            ['name' => 'Delete Subscriptions', 'slug' => 'subscriptions_delete'],
            
            // Tickets Module
            ['name' => 'View Tickets', 'slug' => 'tickets_view'],
            ['name' => 'Create Tickets', 'slug' => 'tickets_create'],
            ['name' => 'Edit Tickets', 'slug' => 'tickets_edit'],
            ['name' => 'Delete Tickets', 'slug' => 'tickets_delete'],
            
            // Items/Products
            ['name' => 'View Items', 'slug' => 'items_view'],
            ['name' => 'Create Items', 'slug' => 'items_create'],
            ['name' => 'Edit Items', 'slug' => 'items_edit'],
            ['name' => 'Delete Items', 'slug' => 'items_delete'],
            
            // Item Groups
            ['name' => 'View Item Groups', 'slug' => 'itemsgroups_view'],
            ['name' => 'Create Item Groups', 'slug' => 'itemsgroups_create'],
            ['name' => 'Edit Item Groups', 'slug' => 'itemsgroups_edit'],
            ['name' => 'Delete Item Groups', 'slug' => 'itemsgroups_delete'],
            
            // Files
            ['name' => 'View Files', 'slug' => 'files_view'],
            ['name' => 'Create Files', 'slug' => 'files_create'],
            ['name' => 'Delete Files', 'slug' => 'files_delete'],
            ['name' => 'Delete All Files', 'slug' => 'files_delete_all'],
            
            // Comments (for Tasks/Projects)
            ['name' => 'Create Comments', 'slug' => 'comments_create'],
            ['name' => 'Edit Comments', 'slug' => 'comments_edit'],
            ['name' => 'Delete Comments', 'slug' => 'comments_delete'],
            
            // Knowledge Base
            ['name' => 'View Knowledge Base', 'slug' => 'knowledgebase_view'],
            ['name' => 'Create Knowledge Base', 'slug' => 'knowledgebase_create'],
            ['name' => 'Edit Knowledge Base', 'slug' => 'knowledgebase_edit'],
            ['name' => 'Delete Knowledge Base', 'slug' => 'knowledgebase_delete'],
            
            // Staff Management
            ['name' => 'View Staff', 'slug' => 'staff_view'],
            ['name' => 'Create Staff', 'slug' => 'staff_create'],
            ['name' => 'Edit Staff', 'slug' => 'staff_edit'],
            ['name' => 'Delete Staff', 'slug' => 'staff_delete'],
            
            // Departments
            ['name' => 'View Departments', 'slug' => 'departments_view'],
            ['name' => 'Create Departments', 'slug' => 'departments_create'],
            ['name' => 'Edit Departments', 'slug' => 'departments_edit'],
            ['name' => 'Delete Departments', 'slug' => 'departments_delete'],
            
            // System Settings
            ['name' => 'View Settings', 'slug' => 'settings_view'],
            ['name' => 'Edit Settings', 'slug' => 'settings_edit'],
            
            // Email Templates
            ['name' => 'View Email Templates', 'slug' => 'emailtemplates_view'],
            ['name' => 'Create Email Templates', 'slug' => 'emailtemplates_create'],
            ['name' => 'Edit Email Templates', 'slug' => 'emailtemplates_edit'],
            ['name' => 'Delete Email Templates', 'slug' => 'emailtemplates_delete'],
            
            // Roles & Permissions
            ['name' => 'View Roles', 'slug' => 'roles_view'],
            ['name' => 'Create Roles', 'slug' => 'roles_create'],
            ['name' => 'Edit Roles', 'slug' => 'roles_edit'],
            ['name' => 'Delete Roles', 'slug' => 'roles_delete'],
            
            // System Lookups
            ['name' => 'View Countries', 'slug' => 'countries_view'],
            ['name' => 'Create Countries', 'slug' => 'countries_create'],
            ['name' => 'Edit Countries', 'slug' => 'countries_edit'],
            ['name' => 'Delete Countries', 'slug' => 'countries_delete'],
            
            ['name' => 'View Currencies', 'slug' => 'currencies_view'],
            ['name' => 'Create Currencies', 'slug' => 'currencies_create'],
            ['name' => 'Edit Currencies', 'slug' => 'currencies_edit'],
            ['name' => 'Delete Currencies', 'slug' => 'currencies_delete'],
            
            ['name' => 'View Services', 'slug' => 'services_view'],
            ['name' => 'Create Services', 'slug' => 'services_create'],
            ['name' => 'Edit Services', 'slug' => 'services_edit'],
            ['name' => 'Delete Services', 'slug' => 'services_delete'],
            
            ['name' => 'View Payment Modes', 'slug' => 'paymentmodes_view'],
            ['name' => 'Create Payment Modes', 'slug' => 'paymentmodes_create'],
            ['name' => 'Edit Payment Modes', 'slug' => 'paymentmodes_edit'],
            ['name' => 'Delete Payment Modes', 'slug' => 'paymentmodes_delete'],
            
            ['name' => 'View Taxes', 'slug' => 'taxes_view'],
            ['name' => 'Create Taxes', 'slug' => 'taxes_create'],
            ['name' => 'Edit Taxes', 'slug' => 'taxes_edit'],
                            ['name' => 'Delete Taxes', 'slug' => 'taxes_delete'],
                            
                            ['name' => 'View Expense Categories', 'slug' => 'expense_categories_view'],
                            ['name' => 'Create Expense Categories', 'slug' => 'expense_categories_create'],
                            ['name' => 'Edit Expense Categories', 'slug' => 'expense_categories_edit'],
                            ['name' => 'Delete Expense Categories', 'slug' => 'expense_categories_delete'],
                            
                            ['name' => 'View Leave Types', 'slug' => 'leave_types_view'],
                            ['name' => 'Create Leave Types', 'slug' => 'leave_types_create'],
                            ['name' => 'Edit Leave Types', 'slug' => 'leave_types_edit'],
                            ['name' => 'Delete Leave Types', 'slug' => 'leave_types_delete'],
                            
                            // HR - Attendance
                            ['name' => 'View Attendance', 'slug' => 'attendance_view'],
                            ['name' => 'Manage Attendance', 'slug' => 'attendance_manage'],

                            // HR - Leaves
                            ['name' => 'View Leaves', 'slug' => 'hr_leaves_view'],
                            ['name' => 'Manage Leaves', 'slug' => 'hr_leaves_manage'],

                            // HR - Payroll
                            ['name' => 'View Payroll', 'slug' => 'hr_payroll_view'],
                            ['name' => 'Manage Payroll', 'slug' => 'hr_payroll_manage'],
                            
                            ['name' => 'View Permissions', 'slug' => 'permissions_view'],
            ['name' => 'Create Permissions', 'slug' => 'permissions_create'],
            ['name' => 'Edit Permissions', 'slug' => 'permissions_edit'],
            ['name' => 'Delete Permissions', 'slug' => 'permissions_delete'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['slug' => $permission['slug']],
                ['name' => $permission['name']]
            );
        }

        $this->command->info('✅ CRM/ERP permissions seeded successfully!');
        $this->command->info('Total permissions: ' . count($permissions));
    }
}