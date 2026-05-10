<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaxSeeder extends Seeder
{
    public function run(): void
    {
        $taxes = [
            ['name' => 'VAT', 'taxrate' => 19.00],
            ['name' => 'Service Tax', 'taxrate' => 5.00],
        ];
        foreach ($taxes as $tax) {
            DB::table('tbltaxes')->updateOrInsert(['name' => $tax['name']], ['taxrate' => $tax['taxrate']]);
        }
    }
}
