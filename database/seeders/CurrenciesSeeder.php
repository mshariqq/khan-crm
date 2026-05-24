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
            ['id' => 3, 'symbol' => '¥', 'name' => 'JPY', 'decimal_separator' => '.', 'thousand_separator' => ',', 'placement' => 'before', 'isdefault' => 0],
            ['id' => 4, 'symbol' => '£', 'name' => 'GBP', 'decimal_separator' => '.', 'thousand_separator' => ',', 'placement' => 'before', 'isdefault' => 0],
            ['id' => 5, 'symbol' => 'A$', 'name' => 'AUD', 'decimal_separator' => '.', 'thousand_separator' => ',', 'placement' => 'before', 'isdefault' => 0],
            ['id' => 6, 'symbol' => 'C$', 'name' => 'CAD', 'decimal_separator' => '.', 'thousand_separator' => ',', 'placement' => 'before', 'isdefault' => 0],
            ['id' => 7, 'symbol' => 'CHF', 'name' => 'CHF', 'decimal_separator' => '.', 'thousand_separator' => ',', 'placement' => 'before', 'isdefault' => 0],
            ['id' => 8, 'symbol' => '¥', 'name' => 'CNY', 'decimal_separator' => '.', 'thousand_separator' => ',', 'placement' => 'before', 'isdefault' => 0],
            ['id' => 9, 'symbol' => '₹', 'name' => 'INR', 'decimal_separator' => '.', 'thousand_separator' => ',', 'placement' => 'before', 'isdefault' => 0],
            ['id' => 10, 'symbol' => 'R$', 'name' => 'BRL', 'decimal_separator' => ',', 'thousand_separator' => '.', 'placement' => 'before', 'isdefault' => 0],
        ];

        foreach ($currencies as $currencyData) {
            $currencyExists = DB::table('tblcurrencies')->where('id', $currencyData['id'])->exists();

            if (!$currencyExists) {
                DB::table('tblcurrencies')->insert($currencyData);
            }
        }
    }
}
