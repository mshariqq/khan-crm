<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Fix tblstaff_departments: column types must match PKs before adding FKs
        DB::statement('ALTER TABLE tblstaff_departments MODIFY staffid BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE tblstaff_departments MODIFY departmentid BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE tblstaff_departments ADD CONSTRAINT tblstaff_departments_staffid_v2_foreign FOREIGN KEY (staffid) REFERENCES tblstaff(staffid) ON DELETE CASCADE');
        DB::statement('ALTER TABLE tblstaff_departments ADD CONSTRAINT tblstaff_departments_departmentid_v2_foreign FOREIGN KEY (departmentid) REFERENCES tbldepartments(departmentid) ON DELETE CASCADE');

        // Fix tblstaff_permissions: staff_id was wrongly set as bigIncrements (PK + auto-increment)
        DB::statement('ALTER TABLE tblstaff_permissions MODIFY staff_id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE tblstaff_permissions DROP PRIMARY KEY');
        DB::statement('ALTER TABLE tblstaff_permissions ADD id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY FIRST');
        DB::statement('ALTER TABLE tblstaff_permissions ADD INDEX tblstaff_permissions_staff_id_index (staff_id)');
        DB::statement('ALTER TABLE tblstaff_permissions ADD CONSTRAINT tblstaff_permissions_staff_id_foreign FOREIGN KEY (staff_id) REFERENCES tblstaff(staffid) ON DELETE CASCADE');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE tblstaff_departments DROP FOREIGN KEY tblstaff_departments_staffid_v2_foreign');
        DB::statement('ALTER TABLE tblstaff_departments DROP FOREIGN KEY tblstaff_departments_departmentid_v2_foreign');
        DB::statement('ALTER TABLE tblstaff_permissions DROP FOREIGN KEY tblstaff_permissions_staff_id_foreign');
        DB::statement('ALTER TABLE tblstaff_permissions DROP INDEX tblstaff_permissions_staff_id_index');
        DB::statement('ALTER TABLE tblstaff_permissions DROP PRIMARY KEY');
        DB::statement('ALTER TABLE tblstaff_permissions DROP COLUMN id');
        DB::statement('ALTER TABLE tblstaff_permissions MODIFY staff_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
        DB::statement('ALTER TABLE tblstaff_permissions ADD PRIMARY KEY (staff_id)');
    }
};
