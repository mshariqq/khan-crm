<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmailTemplatesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tblemailtemplates')->insert([
            [
                'emailtemplateid' => 1,
                'type' => 'client',
                'slug' => 'new-client-created',
                'language' => 'english',
                'name' => 'New Contact Added/Registered (Welcome Email)',
                'subject' => 'Welcome aboard',
                'message' => 'Dear {contact_firstname} {contact_lastname}<br /><br />Thank you for registering...',
                'fromname' => '{companyname} | CRM',
                'fromemail' => '',
                'plaintext' => 0,
                'active' => 1,
                'order' => 0,
            ],
            [
                'emailtemplateid' => 2,
                'type' => 'invoice',
                'slug' => 'invoice-send-to-client',
                'language' => 'english',
                'name' => 'Send Invoice to Customer',
                'subject' => 'Invoice with number {invoice_number} created',
                'message' => 'Dear {contact_firstname} {contact_lastname}<br /><br />We have prepared...',
                'fromname' => '{companyname} | CRM',
                'fromemail' => '',
                'plaintext' => 0,
                'active' => 1,
                'order' => 0,
            ],
        ]);
    }
}
