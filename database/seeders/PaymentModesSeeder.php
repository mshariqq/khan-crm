<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentModesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tblpayment_modes')->insert([
            ['id' => 1, 'name' => 'Bank', 'description' => null, 'show_on_pdf' => 0, 'invoices_only' => 0, 'expenses_only' => 0, 'selected_by_default' => 1, 'active' => 1],
        ]);
    }
}
