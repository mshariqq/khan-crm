<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeadSourcesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tblleads_sources')->insert([
            ['id' => 1, 'name' => 'Google'],
            ['id' => 2, 'name' => 'Facebook'],
        ]);
    }
}
