<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModulesSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            ['module_name' => 'Clients', 'installed_version' => '1.0', 'active' => true],
            ['module_name' => 'Projects', 'installed_version' => '1.0', 'active' => true],
            ['module_name' => 'Leads', 'installed_version' => '1.0', 'active' => true],
            ['module_name' => 'Invoices', 'installed_version' => '1.0', 'active' => true],
            ['module_name' => 'Tasks', 'installed_version' => '1.0', 'active' => true],
            ['module_name' => 'Staff', 'installed_version' => '1.0', 'active' => true],
            ['module_name' => 'Contacts', 'installed_version' => '1.0', 'active' => true],
            ['module_name' => 'Settings', 'installed_version' => '1.0', 'active' => true],
            ['module_name' => 'Tickets', 'installed_version' => '1.0', 'active' => true],
            ['module_name' => 'Proposals', 'installed_version' => '1.0', 'active' => true],
            ['module_name' => 'Estimates', 'installed_version' => '1.0', 'active' => true],
            ['module_name' => 'Expenses', 'installed_version' => '1.0', 'active' => true],
            ['module_name' => 'Credit Notes', 'installed_version' => '1.0', 'active' => true],
            ['module_name' => 'Knowledge Base', 'installed_version' => '1.0', 'active' => true],
            ['module_name' => 'Announcements', 'installed_version' => '1.0', 'active' => true],
            ['module_name' => 'Departments', 'installed_version' => '1.0', 'active' => true],
            ['module_name' => 'Taxes', 'installed_version' => '1.0', 'active' => true],
            ['module_name' => 'Payment Modes', 'installed_version' => '1.0', 'active' => true],
            ['module_name' => 'Items', 'installed_version' => '1.0', 'active' => true],
            ['module_name' => 'Subscriptions', 'installed_version' => '1.0', 'active' => true],
            ['module_name' => 'Item Groups', 'installed_version' => '1.0', 'active' => true],
            ['module_name' => 'Milestones', 'installed_version' => '1.0', 'active' => true],
        ];

        foreach ($modules as $module) {
            DB::table('tblmodules')->updateOrInsert(
                ['module_name' => $module['module_name']],
                ['installed_version' => $module['installed_version'], 'active' => $module['active']]
            );
        }
    }
}
