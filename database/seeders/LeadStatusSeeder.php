<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeadStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['id' => 1, 'name' => 'Customer', 'statusorder' => 1000, 'color' => '#7cb342', 'isdefault' => 1],
            ['id' => 2, 'name' => 'Contacted', 'statusorder' => 100, 'color' => '#2196f3', 'isdefault' => 0],
            ['id' => 3, 'name' => 'Qualified', 'statusorder' => 200, 'color' => '#ff9800', 'isdefault' => 0],
            ['id' => 4, 'name' => 'Proposal Sent', 'statusorder' => 300, 'color' => '#9c27b0', 'isdefault' => 0],
            ['id' => 5, 'name' => 'Lost', 'statusorder' => 500, 'color' => '#f44336', 'isdefault' => 0],
            ['id' => 6, 'name' => 'Junk', 'statusorder' => 600, 'color' => '#795548', 'isdefault' => 0],
        ];

        foreach ($statuses as $status) {
            DB::table('tblleads_status')->updateOrInsert(
                ['id' => $status['id']],
                $status
            );
        }
    }
}
