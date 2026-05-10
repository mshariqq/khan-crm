<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Staff;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class StaffSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Departments
        $departments = [
            ['name' => 'Management', 'email' => 'management@crm.com'],
            ['name' => 'Sales', 'email' => 'sales@crm.com'],
            ['name' => 'Support', 'email' => 'support@crm.com'],
        ];
        
        $departmentIds = [];
        foreach ($departments as $dept) {
            $departmentIds[] = DB::table('tbldepartments')->insertGetId($dept);
        }

        // 2. Create Admin Staff
        $admin = Staff::create([
            'firstname' => 'Admin',
            'lastname' => 'User',
            'email' => 'admin@crm.com',
            'password' => Hash::make('password'),
            'admin' => 1,
            'role' => 1, // Admin role
            'active' => 1,
            'datecreated' => Carbon::now(),
            'default_language' => 'english',
            'direction' => 'ltr',
        ]);

        // 3. Create Regular Staff
        $staffMembers = [
            [
                'firstname' => 'John',
                'lastname' => 'Doe',
                'email' => 'john@crm.com',
                'password' => Hash::make('password'),
                'admin' => 0,
                'role' => 2, // Employee role
                'active' => 1,
                'datecreated' => Carbon::now(),
                'default_language' => 'english',
                'direction' => 'ltr',
            ],
            [
                'firstname' => 'Jane',
                'lastname' => 'Smith',
                'email' => 'jane@crm.com',
                'password' => Hash::make('password'),
                'admin' => 0,
                'role' => 2, // Employee role
                'active' => 1,
                'datecreated' => Carbon::now(),
                'default_language' => 'english',
                'direction' => 'ltr',
            ],
        ];

        $staffIds = [];
        foreach ($staffMembers as $member) {
            $staffIds[] = Staff::create($member)->staffid;
        }

        // 4. Assign Staff to Departments
        DB::table('tblstaff_departments')->insert([
            ['staffid' => $admin->staffid, 'departmentid' => $departmentIds[0]], // Admin -> Management
            ['staffid' => $staffIds[0], 'departmentid' => $departmentIds[1]], // John -> Sales
            ['staffid' => $staffIds[1], 'departmentid' => $departmentIds[2]], // Jane -> Support
        ]);
    }
}
