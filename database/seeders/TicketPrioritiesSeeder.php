<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TicketPrioritiesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tbltickets_priorities')->insert([
            ['priorityid' => 1, 'name' => 'Low'],
            ['priorityid' => 2, 'name' => 'Medium'],
            ['priorityid' => 3, 'name' => 'High'],
        ]);
    }
}
