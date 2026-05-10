<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrenciesSeeder extends Seeder
{
    public function run(): void
    {
        $currencies = [
            ['id' => 1, 'symbol' => '$', 'name' => 'USD', 'decimal_separator' => '.', 'thousand_separator' => ',', 'placement' => 'before', 'isdefault' => 1],
            ['id' => 2, 'symbol' => '€', 'name' => 'EUR', 'decimal_separator' => ',', 'thousand_separator' => '.', 'placement' => 'before', 'isdefault' => 0],
        ];

        foreach ($currencies as $currencyData) {
            $currencyExists = DB::table('tblcurrencies')->where('id', $currencyData['id'])->exists();

            if (!$currencyExists) {
                DB::table('tblcurrencies')->insert($currencyData);
            }
        }
    }
}
