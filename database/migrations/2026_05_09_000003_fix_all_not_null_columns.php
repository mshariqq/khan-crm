<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Fix ALL NOT NULL columns without defaults for all three tables
        
        // === INVOICES ===
        try {
            DB::statement("ALTER TABLE tblinvoices MODIFY `discount_type` VARCHAR(30) NOT NULL DEFAULT 'percent'");
        } catch (\Exception $e) {}
        
        try {
            DB::statement("ALTER TABLE tblinvoices MODIFY `include_shipping` TINYINT(1) NOT NULL DEFAULT 0");
        } catch (\Exception $e) {}
        
        try {
            DB::statement("ALTER TABLE tblinvoices MODIFY `clientid` INT NOT NULL");
        } catch (\Exception $e) {}
        
        try {
            DB::statement("ALTER TABLE tblinvoices MODIFY `number` INT NOT NULL");
        } catch (\Exception $e) {}
        
        try {
            DB::statement("ALTER TABLE tblinvoices MODIFY `datecreated` DATETIME NOT NULL");
        } catch (\Exception $e) {}
        
        try {
            DB::statement("ALTER TABLE tblinvoices MODIFY `date` DATE NOT NULL");
        } catch (\Exception $e) {}
        
        try {
            DB::statement("ALTER TABLE tblinvoices MODIFY `currency` INT NOT NULL");
        } catch (\Exception $e) {}
        
        try {
            DB::statement("ALTER TABLE tblinvoices MODIFY `subtotal` DECIMAL(15,2) NOT NULL DEFAULT 0");
        } catch (\Exception $e) {}
        
        try {
            DB::statement("ALTER TABLE tblinvoices MODIFY `total` DECIMAL(15,2) NOT NULL");
        } catch (\Exception $e) {}
        
        try {
            DB::statement("ALTER TABLE tblinvoices MODIFY `hash` VARCHAR(32) NOT NULL");
        } catch (\Exception $e) {}

        // === ESTIMATES ===
        try {
            DB::statement("ALTER TABLE tblestimates MODIFY `include_shipping` TINYINT(1) NOT NULL DEFAULT 0");
        } catch (\Exception $e) {}
        
        try {
            DB::statement("ALTER TABLE tblestimates MODIFY `clientid` INT NOT NULL");
        } catch (\Exception $e) {}
        
        try {
            DB::statement("ALTER TABLE tblestimates MODIFY `number` INT NOT NULL");
        } catch (\Exception $e) {}
        
        try {
            DB::statement("ALTER TABLE tblestimates MODIFY `datecreated` DATETIME NOT NULL");
        } catch (\Exception $e) {}
        
        try {
            DB::statement("ALTER TABLE tblestimates MODIFY `date` DATE NOT NULL");
        } catch (\Exception $e) {}
        
        try {
            DB::statement("ALTER TABLE tblestimates MODIFY `currency` INT NOT NULL");
        } catch (\Exception $e) {}
        
        try {
            DB::statement("ALTER TABLE tblestimates MODIFY `subtotal` DECIMAL(15,2) NOT NULL DEFAULT 0");
        } catch (\Exception $e) {}
        
        try {
            DB::statement("ALTER TABLE tblestimates MODIFY `total` DECIMAL(15,2) NOT NULL");
        } catch (\Exception $e) {}
        
        try {
            DB::statement("ALTER TABLE tblestimates MODIFY `addedfrom` INT NOT NULL");
        } catch (\Exception $e) {}
        
        try {
            DB::statement("ALTER TABLE tblestimates MODIFY `hash` VARCHAR(32) DEFAULT NULL");
        } catch (\Exception $e) {}

        // === PROPOSALS ===
        try {
            DB::statement("ALTER TABLE tblproposals MODIFY `addedfrom` INT NOT NULL");
        } catch (\Exception $e) {}
        
        try {
            DB::statement("ALTER TABLE tblproposals MODIFY `datecreated` DATETIME NOT NULL");
        } catch (\Exception $e) {}
        
        try {
            DB::statement("ALTER TABLE tblproposals MODIFY `subtotal` DECIMAL(15,2) NOT NULL DEFAULT 0");
        } catch (\Exception $e) {}
        
        try {
            DB::statement("ALTER TABLE tblproposals MODIFY `discount_percent` DECIMAL(15,2) NOT NULL DEFAULT 0");
        } catch (\Exception $e) {}
        
        try {
            DB::statement("ALTER TABLE tblproposals MODIFY `discount_total` DECIMAL(15,2) NOT NULL DEFAULT 0");
        } catch (\Exception $e) {}
        
        try {
            DB::statement("ALTER TABLE tblproposals MODIFY `currency` INT NOT NULL");
        } catch (\Exception $e) {}
        
        try {
            DB::statement("ALTER TABLE tblproposals MODIFY `date` DATE NOT NULL");
        } catch (\Exception $e) {}
        
        try {
            DB::statement("ALTER TABLE tblproposals MODIFY `hash` VARCHAR(32) NOT NULL");
        } catch (\Exception $e) {}
        
        try {
            DB::statement("ALTER TABLE tblproposals MODIFY `status` INT NOT NULL DEFAULT 1");
        } catch (\Exception $e) {}

        // === ITEMABLE TABLE ===
        try {
            DB::statement("ALTER TABLE tblitemable MODIFY `rel_id` INT NOT NULL");
        } catch (\Exception $e) {}
        
        try {
            DB::statement("ALTER TABLE tblitemable MODIFY `rel_type` VARCHAR(15) NOT NULL");
        } catch (\Exception $e) {}
        
        try {
            DB::statement("ALTER TABLE tblitemable MODIFY `description` LONGTEXT NOT NULL");
        } catch (\Exception $e) {}
        
        try {
            DB::statement("ALTER TABLE tblitemable MODIFY `qty` DECIMAL(15,2) NOT NULL DEFAULT 1");
        } catch (\Exception $e) {}
        
        try {
            DB::statement("ALTER TABLE tblitemable MODIFY `rate` DECIMAL(15,2) NOT NULL DEFAULT 0");
        } catch (\Exception $e) {}
    }

    public function down(): void
    {
        // No rollback needed
    }
};