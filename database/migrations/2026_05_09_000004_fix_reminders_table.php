<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        try {
            DB::statement("ALTER TABLE tblreminders MODIFY `staff` INT NOT NULL DEFAULT 0");
        } catch (\Exception $e) {}
        
        try {
            DB::statement("ALTER TABLE tblreminders MODIFY `creator` INT NOT NULL DEFAULT 0");
        } catch (\Exception $e) {}
        
        try {
            DB::statement("ALTER TABLE tblreminders MODIFY `rel_id` INT NOT NULL");
        } catch (\Exception $e) {}
        
        try {
            DB::statement("ALTER TABLE tblreminders MODIFY `rel_type` VARCHAR(40) NOT NULL");
        } catch (\Exception $e) {}
    }

    public function down(): void
    {
    }
};