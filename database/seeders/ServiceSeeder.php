<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            ['name' => 'Consulting'],
            ['name' => 'Support'],
            ['name' => 'Implementation'],
        ];
        foreach ($services as $svc) {
            DB::table('tblservices')->updateOrInsert(['name' => $svc['name']]);
        }
    }
}
