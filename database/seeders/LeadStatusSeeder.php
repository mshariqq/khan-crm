<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeadStatusSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tblleads_status')->insert([
            ['id' => 1, 'name' => 'Customer', 'statusorder' => 1000, 'color' => '#7cb342', 'isdefault' => 1],
        ]);
    }
}
