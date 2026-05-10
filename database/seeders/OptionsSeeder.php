<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OptionsSeeder extends Seeder
{
    /**
     * Comprehensive system settings for CRM/ERP
     * These fields must match the settings form at resources/views/admin/settings/index.blade.php
     */
    public function run(): void
    {
        $options = [
            // General Settings
            ['name' => 'companyname', 'value' => '', 'autoload' => 1],
            ['name' => 'smtp_email', 'value' => '', 'autoload' => 1],
            ['name' => 'companyphone', 'value' => '', 'autoload' => 1],
            ['name' => 'companyaddress', 'value' => '', 'autoload' => 1],
            ['name' => 'company_logo', 'value' => '', 'autoload' => 1],
            ['name' => 'favicon', 'value' => '/favicon.ico', 'autoload' => 1],
            ['name' => 'active_language', 'value' => 'english', 'autoload' => 1],
            ['name' => 'default_timezone', 'value' => 'Europe/Berlin', 'autoload' => 1],
            ['name' => 'dateformat', 'value' => 'Y-m-d|%Y-%m-%d', 'autoload' => 1],
            ['name' => 'decimal_separator', 'value' => '.', 'autoload' => 1],
            ['name' => 'thousand_separator', 'value' => ',', 'autoload' => 1],
            ['name' => 'tables_pagination_limit', 'value' => '25', 'autoload' => 1],
            
            // Email Settings (SMTP)
            ['name' => 'smtp_host', 'value' => 'smtp.gmail.com', 'autoload' => 1],
            ['name' => 'smtp_port', 'value' => '465', 'autoload' => 1],
            ['name' => 'smtp_email', 'value' => '', 'autoload' => 1],
            ['name' => 'smtp_password', 'value' => '', 'autoload' => 1],
            ['name' => 'smtp_encrypt', 'value' => 'ssl', 'autoload' => 1],
            ['name' => 'email_signature', 'value' => '', 'autoload' => 1],
            
            // Financial Settings
            ['name' => 'invoice_prefix', 'value' => 'INV-', 'autoload' => 1],
            ['name' => 'estimate_prefix', 'value' => 'EST-', 'autoload' => 1],
            ['name' => 'credit_note_prefix', 'value' => 'CN-', 'autoload' => 1],
            ['name' => 'invoice_due_after', 'value' => '30', 'autoload' => 1],
            ['name' => 'pdf_logo_width', 'value' => '180', 'autoload' => 1],
            
            // System Settings
            ['name' => 'main_domain', 'value' => '', 'autoload' => 1],
            ['name' => 'session_timeout', 'value' => '7200', 'autoload' => 1],
            ['name' => 'use_knowledge_base', 'value' => '1', 'autoload' => 1],
            ['name' => 'knowledge_base_without_registration', 'value' => '1', 'autoload' => 1],
            ['name' => 'allow_registration', 'value' => '0', 'autoload' => 1],
            
            // Security Settings
            ['name' => 'password_min_length', 'value' => '8', 'autoload' => 1],
            ['name' => 'two_factor_enabled', 'value' => '0', 'autoload' => 1],
            
            // System Features
            ['name' => 'services', 'value' => '1', 'autoload' => 1],
            ['name' => 'use_knowledge_base', 'value' => '1', 'autoload' => 1],
            ['name' => 'maximum_allowed_ticket_attachments', 'value' => '4', 'autoload' => 1],
            ['name' => 'ticket_attachments_file_extensions', 'value' => '.jpg,.jpeg,.png,.pdf,.doc,.docx,.zip,.rar', 'autoload' => 1],
            ['name' => 'staff_access_only_assigned_departments', 'value' => '1', 'autoload' => 1],
            ['name' => 'tables_pagination_limit', 'value' => '25', 'autoload' => 1],
            
            // User Management
            ['name' => 'allow_registration', 'value' => '0', 'autoload' => 1],
            ['name' => 'knowledge_base_without_registration', 'value' => '1', 'autoload' => 1],
            
            // Invoice & Estimate
            ['name' => 'invoice_prefix', 'value' => 'INV-', 'autoload' => 1],
            ['name' => 'estimate_prefix', 'value' => 'EST-', 'autoload' => 1],
            ['name' => 'invoice_auto_operations_hour', 'value' => '09', 'autoload' => 1],
            ['name' => 'invoice_due_after', 'value' => '30', 'autoload' => 1],
            
            // Credit Notes
            ['name' => 'credit_note_prefix', 'value' => 'CN-', 'autoload' => 1],
        ];

        foreach ($options as $option) {
            DB::table('tbloptions')->updateOrInsert(
                ['name' => $option['name']],
                ['value' => $option['value'], 'autoload' => $option['autoload']]
            );
        }

        $this->command->info('✅ Settings seeded successfully!');
        $this->command->info('Seeded ' . count($options) . ' settings');
    }
}