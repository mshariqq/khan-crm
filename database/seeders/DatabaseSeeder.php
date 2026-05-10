<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CountriesSeeder::class,
            CurrenciesSeeder::class,
            PermissionSeeder::class,
            ModulesSeeder::class,
            TaxSeeder::class,
            ServiceSeeder::class,
            RolesSeeder::class,
            PaymentModesSeeder::class,
            TicketPrioritiesSeeder::class,
            TicketStatusSeeder::class,
            LeadStatusSeeder::class,
            LeadSourcesSeeder::class,
            OptionsSeeder::class,
            EmailTemplatesSeeder::class,
            StaffSeeder::class,
        ]);
    }
}
