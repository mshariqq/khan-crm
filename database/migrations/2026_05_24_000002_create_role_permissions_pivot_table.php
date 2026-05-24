<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tblrole_permissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('permission_id');
            $table->timestamps();
            $table->unique(['role_id', 'permission_id']);
            $table->foreign('role_id')->references('roleid')->on('tblroles')->cascadeOnDelete();
            $table->foreign('permission_id')->references('id')->on('tblpermissions')->cascadeOnDelete();
        });

        // Migrate existing comma-separated permissions to pivot table
        $roles = DB::table('tblroles')->whereNotNull('permissions')->where('permissions', '!=', '')->get();
        foreach ($roles as $role) {
            $slugs = array_map('trim', explode(',', $role->permissions));
            $permissionIds = DB::table('tblpermissions')->whereIn('slug', $slugs)->pluck('id');
            foreach ($permissionIds as $permId) {
                DB::table('tblrole_permissions')->insert([
                    'role_id' => $role->roleid,
                    'permission_id' => $permId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tblrole_permissions');
    }
};
