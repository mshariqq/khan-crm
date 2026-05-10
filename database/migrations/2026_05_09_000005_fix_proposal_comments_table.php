<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        try {
            DB::statement("ALTER TABLE tblproposal_comments MODIFY `staffid` INT NOT NULL DEFAULT 0");
        } catch (\Exception $e) {}
        
        try {
            DB::statement("ALTER TABLE tblproposal_comments MODIFY `proposalid` INT NOT NULL");
        } catch (\Exception $e) {}
        
        try {
            DB::statement("ALTER TABLE tblproposal_comments MODIFY `dateadded` DATETIME NOT NULL");
        } catch (\Exception $e) {}
    }

    public function down(): void
    {
    }
};