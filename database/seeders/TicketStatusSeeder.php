<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TicketStatusSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tbltickets_status')->insert([
            ['ticketstatusid' => 1, 'name' => 'Open', 'isdefault' => 1, 'statuscolor' => '#ff2d42', 'statusorder' => 1],
            ['ticketstatusid' => 2, 'name' => 'In progress', 'isdefault' => 1, 'statuscolor' => '#22c55e', 'statusorder' => 2],
            ['ticketstatusid' => 3, 'name' => 'Answered', 'isdefault' => 1, 'statuscolor' => '#2563eb', 'statusorder' => 3],
            ['ticketstatusid' => 4, 'name' => 'On Hold', 'isdefault' => 1, 'statuscolor' => '#64748b', 'statusorder' => 4],
            ['ticketstatusid' => 5, 'name' => 'Closed', 'isdefault' => 1, 'statuscolor' => '#03a9f4', 'statusorder' => 5],
        ]);
    }
}
